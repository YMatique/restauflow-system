<?php

namespace App\Livewire\POS;

use App\Models\Product;
use App\Models\Table;
use App\Livewire\POS\TablesList;
use Livewire\Component;

class POSComponent extends Component
{
    public $currentTable = null;
    public $cart = [];

    protected $listeners = [
        'tableSelected' => 'handleTableSelection',
        'productAdded' => 'handleProductAddition',
        'cartUpdated' => 'handleCartUpdate',
        'paymentCompleted' => 'handlePaymentCompletion',
        'shiftClosed' => 'handleShiftClosed',
    ];

    public function mount()
    {
        // Verifica turno ativo
        if (!auth()->user()->getActiveShift()) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'É necessário ter um turno ativo para usar o POS'
            ]);
            return redirect()->route('restaurant.shifts');
        }
    }

    // ===== TABLE MANAGEMENT =====

    public function openTableModal()
    {
        
        // Dispara evento para abrir modal de mesas
        // $this->dispatch('openTableModal')->to(TablesList::class);
        $this->dispatch('openTableModal');
    }

    // ===== HANDLERS DE EVENTOS =====

    public function handleTableSelection($tableId)
    {
        $table = \App\Models\Table::find($tableId);
        $this->currentTable = $table;
        $this->showTableModal = false; // Fecha o modal
    }

    public function handleProductAddition($productId, $quantity = 1)
    {
        $product = Product::find($productId);
        
        if (!$product) return;

        $cartKey = $productId;

        if (isset($this->cart[$cartKey])) {
            $this->cart[$cartKey]['quantity'] += $quantity;
            $this->cart[$cartKey]['total_price'] = 
                $this->cart[$cartKey]['quantity'] * $this->cart[$cartKey]['unit_price'];
        } else {
            $this->cart[$cartKey] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'unit_price' => $product->price,
                'quantity' => $quantity,
                'total_price' => $product->price * $quantity
            ];
        }

        // Notifica outros componentes sobre atualização do carrinho
        $this->dispatch('cartChanged', cart: $this->cart);
    }

    public function handleCartUpdate($cartKey, $newQuantity)
    {
        if ($newQuantity <= 0) {
            unset($this->cart[$cartKey]);
        } else {
            // Validar stock
            $product = Product::find($this->cart[$cartKey]['product_id']);
            if (!$product->canSell($newQuantity)) {
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => 'Quantidade não disponível em stock'
                ]);
                return;
            }

            $this->cart[$cartKey]['quantity'] = $newQuantity;
            $this->cart[$cartKey]['total_price'] = 
                $newQuantity * $this->cart[$cartKey]['unit_price'];
        }

        $this->dispatch('cartChanged', cart: $this->cart);
    }

    public function handlePaymentCompletion($saleId)
    {
        // Reset após venda
        $this->cart = [];
        $this->currentTable = null;
        
        // Limpa seleção de mesa
        $this->dispatch('tableCleared');
        
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Venda concluída com sucesso!'
        ]);
    }

    public function handleShiftClosed()
    {
        return redirect()->route('restaurant.shifts');
    }

    // ===== COMPUTED PROPERTIES =====

    public function getCartTotalProperty()
    {
        return collect($this->cart)->sum('total_price');
    }

    public function getCartCountProperty()
    {
        return array_sum(array_column($this->cart, 'quantity'));
    }

    public function getActiveShiftProperty()
    {
        return auth()->user()->getActiveShift();
    }

    public function render()
    {
        return view('livewire.p-o-s.p-o-s-component', [
            'cartTotal' => $this->cartTotal,
            'cartCount' => $this->cartCount,
            'activeShift' => $this->activeShift,
        ])->layout('layouts.pos');
    }
}
