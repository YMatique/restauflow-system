<?php

namespace App\Livewire\POS;

use App\Models\CashMovement;
use App\Models\Product;
use App\Models\Sale;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PaymentModal extends Component
{
        public $show = false;
    public $cart = [];
    public $currentTable = null;

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

    // Mixed Payment
    public $mixedPayments = [];
    public $showMixedPayment = false;

    protected $listeners = [
        'openPaymentModal' => 'open',
        'cartChanged' => 'updateCart'
    ];

    public function updateCart($cart)
    {
        $this->cart = $cart;
        $this->updatePaymentCalculations();
    }

    public function open()
    {
        $this->show = true;
        $this->updatePaymentCalculations();
        $this->paymentForm['received_amount'] = $this->paymentForm['total_amount'];
    }

    public function close()
    {
        $this->show = false;
        $this->showMixedPayment = false;
        $this->reset('paymentForm', 'mixedPayments');
    }

    public function updatePaymentCalculations()
    {
        $subtotal = collect($this->cart)->sum('total_price');
        $serviceCharge = $this->paymentForm['service_charge'];
        $discount = $this->paymentForm['discount_amount'];

        $this->paymentForm['total_amount'] = $subtotal + $serviceCharge - $discount;

        if ($this->paymentForm['payment_method'] === 'cash') {
            $this->paymentForm['change_amount'] = max(0, 
                $this->paymentForm['received_amount'] - $this->paymentForm['total_amount']
            );
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

        if ($this->paymentForm['payment_method'] !== 'credit' && 
            $this->paymentForm['received_amount'] < $this->paymentForm['total_amount']) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Valor recebido é insuficiente'
            ]);
            return;
        }

        try {
            DB::beginTransaction();

            $sale = $this->createSale();
            $this->createSaleItems($sale);
            $this->updateStock($sale);
            $this->createCashMovement($sale);

            DB::commit();

            $this->dispatch('paymentCompleted', saleId: $sale->id);
            $this->close();

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Venda processada com sucesso!'
            ]);
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

        return Sale::create([
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

            $product->update(['stock_quantity' => $newStock]);

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

        $activeShift->increment('total_sales', $sale->total);
        $activeShift->increment('total_orders');
    }
    public function render()
    {
        return view('livewire.p-o-s.payment-modal');
    }
}
