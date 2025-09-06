<?php

namespace App\Livewire\POS;

use App\Models\CashMovement;
use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\StockMovement;
use App\Models\Table;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class POSComponent extends Component
{

    public $currentTable = null;
    public $selectedCategory = null;
    public $cart = [];
    public $showTableModal = false;
    public $showPaymentModal = false;

    // Cash Management Modals
    public $showWithdrawalModal = false;
    public $showCloseShiftModal = false;

    // Payment Form
    public $paymentForm = [
        'payment_method' => 'cash',
        'total_amount' => 0,
        'received_amount' => 0,
        'change_amount' => 0,
        'notes' => '',
        'customer_count' => 1,
        'service_charge' => 0,
        'discount_amount' => 0
    ];

    // Cash Management Forms
    public $withdrawalForm = [
        'amount' => 0,
        'description' => ''
    ];

    public $closeShiftForm = [
        'final_amount' => 0,
        'closing_notes' => ''
    ];

    // Mixed Payment
    public $mixedPayments = [];
    public $showMixedPayment = false;

    public function mount()
    {
        // Check if user has active shift
        if (!auth()->user()->getActiveShift()) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'É necessário ter um turno ativo para usar o POS'
            ]);
            return redirect()->route('restaurant.shifts');
        }

        // Get first active category
        $this->selectedCategory = Category::active()
            ->byCompany(auth()->user()->company_id)
            ->first()?->id;
    }

    // ===== CASH MANAGEMENT METHODS =====

    public function openWithdrawalModal()
    {
        $this->showWithdrawalModal = true;
        $this->reset('withdrawalForm');
    }

    public function closeWithdrawalModal()
    {
        $this->showWithdrawalModal = false;
        $this->reset('withdrawalForm');
    }

    public function registerWithdrawal()
    {
        $this->validate([
            'withdrawalForm.amount' => 'required|numeric|min:0.01',
            'withdrawalForm.description' => 'required|string|min:3|max:255'
        ]);

        $activeShift = auth()->user()->getActiveShift();

        try {
            // Create cash movement
            CashMovement::create([
                'shift_id' => $activeShift->id,
                'type' => 'out',
                'amount' => $this->withdrawalForm['amount'],
                'description' => $this->withdrawalForm['description'],
                'category' => 'withdrawal',
                'date' => now(),
                'user_id' => auth()->id(),
                'company_id' => auth()->user()->company_id
            ]);

            // Update shift withdrawals
            $activeShift->increment('withdrawals', $this->withdrawalForm['amount']);

            $this->closeWithdrawalModal();

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Retirada registrada com sucesso!'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erro ao registrar retirada: ' . $e->getMessage()
            ]);
        }
    }

    public function openCloseShiftModal()
    {
        $activeShift = auth()->user()->getActiveShift();
        // dd($activeShift);


        try {
            // Calculate expected amount
            $expectedAmount = $activeShift->initial_amount +
                ($activeShift->getSalesByPaymentMethod()['cash'] ?? 0) -
                ($activeShift->withdrawals ?? 0);

            $this->closeShiftForm['final_amount'] = $expectedAmount;
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Turno fechado com sucesso'
            ]);
            
        } catch (\Throwable $th) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erro ao fechar turno: ' . $th->getMessage()
            ]);
        }
        $this->showCloseShiftModal = true;


    }

    public function closeCloseShiftModal()
    {
        $this->showCloseShiftModal = false;
        $this->reset('closeShiftForm');
    }

    public function closeShift()
    {
        $this->validate([
            'closeShiftForm.final_amount' => 'required|numeric|min:0',
            'closeShiftForm.closing_notes' => 'nullable|string|max:500'
        ]);

        $activeShift = auth()->user()->getActiveShift();

        try {
            $activeShift->close(
                $this->closeShiftForm['final_amount'],
                $this->closeShiftForm['closing_notes'],
                $activeShift->withdrawals ?? 0
            );

            $this->closeCloseShiftModal();

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Turno fechado com sucesso! Redirecionando...'
            ]);

            // Redirect to shift management after 2 seconds
            $this->dispatch('redirect-after-delay', [
                'url' => route('restaurant.shifts'),
                'delay' => 2000
            ]);
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erro ao fechar turno: ' . $e->getMessage()
            ]);
        }
    }

    public function getCurrentCashBalance()
    {
        $activeShift = auth()->user()->getActiveShift();
        if (!$activeShift) return 0;

        return $activeShift->initial_amount +
            ($activeShift->getSalesByPaymentMethod()['cash'] ?? 0) -
            ($activeShift->withdrawals ?? 0);
    }

    public function getRecentCashMovements()
    {
        $activeShift = auth()->user()->getActiveShift();
        if (!$activeShift) return collect();

        return $activeShift->cashMovements()
            ->latest()
            ->limit(5)
            ->get();
    }

    // ===== TABLE MANAGEMENT =====
    public function selectTable($tableId)
    {
        $table = Table::find($tableId);
        if ($table && $table->isAvailable()) {
            $this->currentTable = $table;
            $this->showTableModal = false;

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => "Mesa {$table->name} selecionada"
            ]);
        } else {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Mesa não disponível'
            ]);
        }
    }

    public function openTableModal()
    {
        $this->showTableModal = true;
    }

    public function closeTableModal()
    {
        $this->showTableModal = false;
    }

    // ===== CATEGORY & PRODUCT MANAGEMENT =====
    public function selectCategory($categoryId)
    {
        $this->selectedCategory = $categoryId;
    }

    public function addToCart($productId, $quantity = 1)
    {
        $product = Product::find($productId);

        if (!$product) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Produto não encontrado'
            ]);
            return;
        }

        if (!$product->canSell($quantity)) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Produto indisponível ou sem stock suficiente'
            ]);
            return;
        }

        $cartKey = $productId;

        if (isset($this->cart[$cartKey])) {
            $this->cart[$cartKey]['quantity'] += $quantity;
            $this->cart[$cartKey]['total_price'] = $this->cart[$cartKey]['quantity'] * $this->cart[$cartKey]['unit_price'];
        } else {
            $this->cart[$cartKey] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'unit_price' => $product->price,
                'quantity' => $quantity,
                'total_price' => $product->price * $quantity
            ];
        }

        $this->updatePaymentCalculations();

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => "{$product->name} adicionado ao carrinho"
        ]);
    }

    public function updateCartQuantity($cartKey, $newQuantity)
    {
        if ($newQuantity <= 0) {
            unset($this->cart[$cartKey]);
            $this->dispatch('toast', [
                'type' => 'info',
                'message' => 'Item removido do carrinho'
            ]);
        } else {
            // Check stock availability
            $product = Product::find($this->cart[$cartKey]['product_id']);
            if (!$product->canSell($newQuantity)) {
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => 'Quantidade não disponível em stock'
                ]);
                return;
            }

            $this->cart[$cartKey]['quantity'] = $newQuantity;
            $this->cart[$cartKey]['total_price'] = $newQuantity * $this->cart[$cartKey]['unit_price'];
        }

        $this->updatePaymentCalculations();
    }

    public function clearCart()
    {
        $this->cart = [];
        $this->updatePaymentCalculations();

        $this->dispatch('toast', [
            'type' => 'info',
            'message' => 'Carrinho limpo'
        ]);
    }

    // ===== PAYMENT MANAGEMENT =====
    public function openPaymentModal()
    {
        if (empty($this->cart)) {
            $this->dispatch('toast', [
                'type' => 'warning',
                'message' => 'Carrinho vazio'
            ]);
            return;
        }

        if (!$this->currentTable) {
            $this->dispatch('toast', [
                'type' => 'warning',
                'message' => 'Selecione uma mesa primeiro'
            ]);
            return;
        }

        $this->updatePaymentCalculations();
        $this->paymentForm['received_amount'] = $this->paymentForm['total_amount'];
        $this->showPaymentModal = true;
    }

    public function closePaymentModal()
    {
        $this->showPaymentModal = false;
        $this->showMixedPayment = false;
        $this->mixedPayments = [];
    }

    public function updatePaymentCalculations()
    {
        $subtotal = collect($this->cart)->sum('total_price');
        $serviceCharge = $this->paymentForm['service_charge'];
        $discount = $this->paymentForm['discount_amount'];

        $this->paymentForm['total_amount'] = $subtotal + $serviceCharge - $discount;

        // Calculate change for cash payments
        if ($this->paymentForm['payment_method'] === 'cash') {
            $this->paymentForm['change_amount'] = max(0, $this->paymentForm['received_amount'] - $this->paymentForm['total_amount']);
        } else {
            $this->paymentForm['change_amount'] = 0;
        }
    }

    public function updatedPaymentFormReceivedAmount()
    {
        $this->updatePaymentCalculations();
    }

    public function updatedPaymentFormServiceCharge()
    {
        $this->updatePaymentCalculations();
    }

    public function updatedPaymentFormDiscountAmount()
    {
        $this->updatePaymentCalculations();
    }

    public function updatedPaymentFormPaymentMethod($value)
    {
        if ($value === 'mixed') {
            $this->showMixedPayment = true;
            $this->initializeMixedPayments();
        } else {
            $this->showMixedPayment = false;
            $this->mixedPayments = [];
        }
        $this->updatePaymentCalculations();
    }

    public function initializeMixedPayments()
    {
        $this->mixedPayments = [
            ['method' => 'cash', 'amount' => 0],
            ['method' => 'card', 'amount' => 0]
        ];
    }

    // ===== PROCESS SALE =====
    public function processPayment()
    {
        $this->validate([
            'paymentForm.payment_method' => 'required|in:cash,card,mbway,mpesa,transfer,mixed',
            'paymentForm.received_amount' => 'required|numeric|min:0',
            'paymentForm.customer_count' => 'required|integer|min:1',
            'paymentForm.service_charge' => 'nullable|numeric|min:0',
            'paymentForm.discount_amount' => 'nullable|numeric|min:0'
        ]);

        if (empty($this->cart)) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Carrinho vazio'
            ]);
            return;
        }

        // Validate payment amount
        if (
            $this->paymentForm['payment_method'] !== 'credit' &&
            $this->paymentForm['received_amount'] < $this->paymentForm['total_amount']
        ) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Valor recebido é insuficiente'
            ]);
            return;
        }

        // Process sale in database transaction
        try {
            DB::beginTransaction();

            $sale = $this->createSale();
            $this->createSaleItems($sale);
            $this->updateStock($sale);
            $this->createCashMovement($sale);

            // Update table status if needed
            if ($this->currentTable) {
                $this->currentTable->markOccupied();
            }

            DB::commit();

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Venda processada com sucesso!'
            ]);

            // Reset form
            $this->resetAfterSale();

            // Optional: Print receipt or redirect
            $this->dispatch('sale-completed', ['saleId' => $sale->id]);
        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erro ao processar venda: ' . $e->getMessage()
            ]);
        }
    }

    private function createSale()
    {
        $activeShift = auth()->user()->getActiveShift();
        $company = auth()->user()->company;
        $subtotal = collect($this->cart)->sum('total_price');
        $taxAmount = $subtotal * ($company->tax_rate / 100);

        $sale = Sale::create([
            'invoice_number' => $company->generateInvoiceNumber(),
            'table_id' => $this->currentTable?->id,
            'shift_id' => $activeShift->id,
            'user_id' => auth()->id(),
            'sold_at' => now(),
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => $this->paymentForm['discount_amount'] ?? 0,
            'service_charge' => $this->paymentForm['service_charge'] ?? 0,
            'total' => $this->paymentForm['total_amount'],
            'payment_method' => $this->paymentForm['payment_method'],
            'payment_details' => $this->paymentForm['payment_method'] === 'mixed' ? $this->mixedPayments : null,
            'status' => 'completed',
            'notes' => $this->paymentForm['notes'],
            'sale_type' => 'dine_in',
            'customer_count' => $this->paymentForm['customer_count'],
            'company_id' => auth()->user()->company_id,
            'completed_at' => now()
        ]);

        return $sale;
    }

    private function createSaleItems($sale)
    {
        foreach ($this->cart as $item) {
            $sale->saleItems()->create([
                'product_id' => $item['product_id'],
                'product_name' => $item['product_name'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total_price' => $item['total_price'],
                'company_id' => auth()->user()->company_id
            ]);
        }
    }

    private function updateStock($sale)
    {
        foreach ($this->cart as $item) {
            $product = Product::find($item['product_id']);
            $previousStock = $product->stock_quantity;
            $newStock = $previousStock - $item['quantity'];

            // Update product stock
            $product->update(['stock_quantity' => $newStock]);

            // Create stock movement
            StockMovement::create([
                'product_id' => $product->id,
                'type' => 'out',
                'quantity' => $item['quantity'],
                'previous_stock' => $previousStock,
                'new_stock' => $newStock,
                'reference_type' => Sale::class,
                'reference_id' => $sale->id,
                'reason' => 'sale',
                'notes' => "Venda #{$sale->invoice_number}",
                'date' => now(),
                'user_id' => auth()->id(),
                'company_id' => auth()->user()->company_id
            ]);
        }
    }

    private function createCashMovement($sale)
    {
        $activeShift = auth()->user()->getActiveShift();

        // Create cash in movement for the sale
        CashMovement::create([
            'shift_id' => $activeShift->id,
            'type' => 'in',
            'amount' => $sale->total,
            'description' => "Venda #{$sale->invoice_number}",
            'category' => 'sale',
            'reference_type' => Sale::class,
            'reference_id' => $sale->id,
            'date' => now(),
            'user_id' => auth()->id(),
            'company_id' => auth()->user()->company_id
        ]);

        // Update shift totals
        $activeShift->increment('total_sales', $sale->total);
        $activeShift->increment('total_orders');
    }

    private function resetAfterSale()
    {
        $this->cart = [];
        $this->currentTable = null;
        $this->showPaymentModal = false;
        $this->showMixedPayment = false;
        $this->mixedPayments = [];

        $this->paymentForm = [
            'payment_method' => 'cash',
            'total_amount' => 0,
            'received_amount' => 0,
            'change_amount' => 0,
            'notes' => '',
            'customer_count' => 1,
            'service_charge' => 0,
            'discount_amount' => 0
        ];
    }

    public function render()
    {

        $companyId = auth()->user()->company_id;
        $activeShift = auth()->user()->getActiveShift();

        // dd(Table::active()->get());
        return view('livewire.p-o-s.p-o-s-component', [
            'categories' => Category::active()
                ->byCompany($companyId)
                ->ordered()
                ->get(),
            'products' => Product::active()
                ->available()
                ->byCompany($companyId)
                ->when($this->selectedCategory, fn($q) => $q->where('category_id', $this->selectedCategory))
                ->get(),
            'tables' => Table::active()
                ->byCompany($companyId)
                ->get(),
            'cartTotal' => collect($this->cart)->sum('total_price'),
            'cartCount' => array_sum(array_column($this->cart, 'quantity')),
            'currentTable' => $this->currentTable,
            'activeShift' => $activeShift,
            'shiftInfo' => $activeShift ? $activeShift->opened_at->format('H:i') . ' - Ativo' : 'Sem turno',
            'currentCashBalance' => $this->getCurrentCashBalance(),
            'recentMovements' => $this->getRecentCashMovements()
        ])->layout('layouts.pos');
    }
}
