<?php

namespace App\Livewire\POS;

use App\Models\Product;
use App\Models\Table;
use Livewire\Attributes\On;
use Livewire\Component;

class OrderSummary extends Component
{
    // Propriedade injetada pelo POSComponent (aqui usamos o cast para simplificar)
    public $currentTable = null; 
    
    // O carrinho é uma propriedade LOCAL do OrderSummary (a chave para a performance)
    public array $cart = []; 

    protected $listeners = [
        // Ouve o evento DIRETO do ProductsGrid
        'productAddedToCart' => 'handleProductAddition', 
    ];

    // MODIFICADO: Centraliza a lógica de adição. O POSComponent não se envolve.
     #[On('productAddedToCart')]
    public function handleProductAddition(int $productId, int $quantity = 1, $tableId = null): void
    {

        // dd($this->currentTable);

         if (!$tableId) {
        $this->dispatch('toast', ['type' => 'warning', 'message' => 'Selecione uma mesa primeiro.']);
        return;
    }
    $this->currentTable = Table::find($tableId); 
        if (!$this->currentTable) {
            $this->dispatch('toast', ['type' => 'warning', 'message' => 'Selecione uma mesa primeiro.']);
            return;
        }
  // $this->currentTable = Table::find($tableId); 
        $product = Product::find($productId);
        
        if (!$product) {
             $this->dispatch('toast', ['type' => 'error', 'message' => 'Produto não encontrado.']);
             return;
        }

        $cartKey = $productId; // Usar o ID do produto como chave

        if (isset($this->cart[$cartKey])) {
            $this->cart[$cartKey]['quantity'] += $quantity;
        } else {
            $this->cart[$cartKey] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'unit_price' => $product->price,
                'quantity' => $quantity,
            ];
        }
        
        // Recalcular total (Local e Rápido)
        $this->cart[$cartKey]['total_price'] = 
            $this->cart[$cartKey]['quantity'] * $this->cart[$cartKey]['unit_price'];

        $this->dispatch('toast', ['type' => 'success', 'message' => "{$product->name} adicionado!"]);
    }

    // MODIFICADO: Atualização LOCAL e RÁPIDA da quantidade.
    public function updateQuantity(int $cartKey, int $newQuantity): void
    {
        // Se a nova quantidade for zero ou menos, remove o item
        if ($newQuantity <= 0) {
            if (isset($this->cart[$cartKey])) {
                unset($this->cart[$cartKey]);
                $this->dispatch('toast', ['type' => 'info', 'message' => 'Item removido do carrinho.']);
            }
        } elseif (isset($this->cart[$cartKey])) {
            $this->cart[$cartKey]['quantity'] = $newQuantity;
            $this->cart[$cartKey]['total_price'] = $newQuantity * $this->cart[$cartKey]['unit_price'];
        }
        
        // Se o carrinho ficar vazio, avisa o POSComponent para talvez liberar a mesa
        if (empty($this->cart) && $this->currentTable) {
            $this->dispatch('cartEmptied', tableId: $this->currentTable->id)
                 ->to(\App\Livewire\POS\POSComponent::class); 
        }
    }

    public function removeItem(int $cartKey): void
    {
        $this->updateQuantity($cartKey, 0);
    }

    public function clearCart(): void
    {
        if (empty($this->cart)) {
            $this->dispatch('toast', ['type' => 'warning', 'message' => 'Carrinho já está vazio.']);
            return;
        }

        $this->cart = [];
        
        // Avisa o POSComponent que o carrinho foi limpo
        if ($this->currentTable) {
            $this->dispatch('cartEmptied', tableId: $this->currentTable->id)
                 ->to(\App\Livewire\POS\POSComponent::class);
        }
        
        $this->dispatch('toast', ['type' => 'info', 'message' => 'Carrinho limpo.']);
    }

    public function openPayment(): void
    {
        if (empty($this->cart)) {
            $this->dispatch('toast', ['type' => 'warning', 'message' => 'Carrinho vazio.']);
            return;
        }

        if (!$this->currentTable) {
            $this->dispatch('toast', ['type' => 'warning', 'message' => 'Selecione uma mesa primeiro.']);
            return;
        }

        // Simulação de Finalizar/Salvar e abrir Modal
        $this->finalizeOrder(); 
    }

    public function finalizeOrder(): void
    {
        $this->cart = []; // Limpa o carrinho localmente
        // Avisa o POSComponent que a venda foi concluída
        $this->dispatch('orderFinalized')
             ->to(\App\Livewire\POS\POSComponent::class); 
    }

    public function getCartTotalProperty(): float
    {
        return collect($this->cart)->sum('total_price');
    }

    public function getCartCountProperty(): int
    {
        return collect($this->cart)->sum('quantity');
    }
    public function render()
    {
        return view('livewire.p-o-s.order-summary',[
             'cartTotal' => $this->cartTotal, // Acedido via $this->cartTotal
        'cartCount' => $this->cartCount 
        ]);
    }
}
