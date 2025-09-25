<?php

namespace App\Livewire\POS;

use App\Models\Product;
use App\Models\Table;
use Livewire\Attributes\On;
use Livewire\Component;

class OrderSummary extends Component
{
 public ?Table $currentTable = null; 
    public array $cart = []; 
    public int $currentTableId = 0; // ID da mesa para controle interno

    protected $listeners = [
        'productAddedToCart' => 'handleProductAddition',
        'tableSelected' => 'handleTableUpdate', // Escuta diretamente a seleção
    ];

    public function mount($currentTable = null)
    {
        if ($currentTable) {
            $this->currentTable = $currentTable;
            $this->currentTableId = $currentTable->id;
        }
        logger("OrderSummary mounted com mesa: " . ($currentTable ? $currentTable->name : 'null'));
    }

    // Escuta mudanças de mesa diretamente
    public function handleTableUpdate($tableId)
    {
        // dd($tableId);
        // logger("OrderSummary: Recebendo nova mesa ID: {$tableId}");
                $tableId = (int) $tableId;

        if ($tableId !== $this->currentTableId) {
            $this->currentTable = Table::find($tableId);
            $this->currentTableId = $tableId;
            
            // Opcional: limpar carrinho ao mudar mesa
            // $this->cart = [];
            
            logger("OrderSummary: Mesa atualizada para: " . ($this->currentTable ? $this->currentTable->name : 'null'));
        }
    }

    public function updatedCurrentTable($value)
    {
        if ($value && $value->id !== $this->currentTableId) {
            $this->currentTableId = $value->id;
            logger("OrderSummary: currentTable prop atualizada para: " . $value->name);
        }
    }

    #[On('productAddedToCart')]
    public function handleProductAddition(int $productId, int $quantity = 1, $tableId = null): void
    {
        logger("OrderSummary: Adicionando produto {$productId} para mesa {$tableId}");

        // Usar o tableId passado ou o atual
        $targetTableId = $tableId ?? $this->currentTableId;
        
        if (!$targetTableId) {
            $this->dispatch('toast', ['type' => 'warning', 'message' => 'Selecione uma mesa primeiro.']);
            return;
        }
        
        // Garantir que temos a mesa atualizada
        if (!$this->currentTable || $this->currentTable->id !== $targetTableId) {
            $this->currentTable = Table::find($targetTableId);
            $this->currentTableId = $targetTableId;
        }
        
        if (!$this->currentTable) {
            $this->dispatch('toast', ['type' => 'warning', 'message' => 'Mesa não encontrada.']);
            return;
        }

        $product = Product::find($productId);
        
        if (!$product) {
             $this->dispatch('toast', ['type' => 'error', 'message' => 'Produto não encontrado.']);
             return;
        }

        $cartKey = $productId;

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
        
        $this->cart[$cartKey]['total_price'] = 
            $this->cart[$cartKey]['quantity'] * $this->cart[$cartKey]['unit_price'];

        $this->dispatch('toast', ['type' => 'success', 'message' => "{$product->name} adicionado!"]);
    }

    public function updateQuantity(int $cartKey, int $newQuantity): void
    {
        if ($newQuantity <= 0) {
            if (isset($this->cart[$cartKey])) {
                unset($this->cart[$cartKey]);
                $this->dispatch('toast', ['type' => 'info', 'message' => 'Item removido do carrinho.']);
            }
        } elseif (isset($this->cart[$cartKey])) {
            $this->cart[$cartKey]['quantity'] = $newQuantity;
            $this->cart[$cartKey]['total_price'] = $newQuantity * $this->cart[$cartKey]['unit_price'];
        }
        
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

        $this->finalizeOrder(); 
    }

    public function finalizeOrder(): void
    {
        $this->cart = [];
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
         logger("OrderSummary render - Mesa atual: " . ($this->currentTable ? $this->currentTable->name : 'null'));
        
        return view('livewire.p-o-s.order-summary', [
            'cartTotal' => $this->cartTotal, // Acedido via $this->cartTotal
            'cartCount' => $this->cartCount,
        ]);
    }
}
