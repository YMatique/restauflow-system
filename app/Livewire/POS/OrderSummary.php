<?php

namespace App\Livewire\POS;

use Livewire\Component;

class OrderSummary extends Component
{
  public $cart = [];
    public $currentTable = null;

    protected $listeners = [
        'cartChanged' => 'updateCart'
    ];

    public function updateCart($cart)
    {
        $this->cart = $cart;
    }

    public function updateQuantity($cartKey, $newQuantity)
    {
        // Envia evento para componente pai atualizar
        $this->dispatch('cartUpdated', 
            cartKey: $cartKey, 
            newQuantity: $newQuantity
        );
    }

    public function removeItem($cartKey)
    {
        $this->updateQuantity($cartKey, 0);
    }

    public function clearCart()
    {
        if (empty($this->cart)) {
            $this->dispatch('toast', [
                'type' => 'warning',
                'message' => 'Carrinho já está vazio'
            ]);
            return;
        }

        $this->cart = [];
        $this->dispatch('cartUpdated', cartKey: null, newQuantity: 0);
        
        $this->dispatch('toast', [
            'type' => 'info',
            'message' => 'Carrinho limpo'
        ]);
    }

    public function openPayment()
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

        // Abre modal de pagamento
        $this->dispatch('openPaymentModal');
    }

    public function getCartTotalProperty()
    {
        return collect($this->cart)->sum('total_price');
    }

    public function getCartCountProperty()
    {
        return collect($this->cart)->sum('quantity');
    }

    public function render()
    {
        return view('livewire.p-o-s.order-summary',[
            'cartTotal' => $this->cartTotal,
            'cartCount' => $this->cartCount
        ]);
    }
}
