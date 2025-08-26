<?php

namespace App\Livewire\POS;

use App\Models\Category;
use App\Models\Product;
use App\Models\Table;
use Livewire\Component;

class POSComponent extends Component
{
     public $currentTable = null;
    public $selectedCategory = null;
    public $cart = [];
    public $showTableModal = false;
    
    public function mount()
    {
        // Pegar primeira categoria ativa da empresa do usuário
        $this->selectedCategory = Category::active()
            ->byCompany(auth()->user()->company_id)
            ->first()?->id;
    }
    
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
        $this->dispatch('table-modal-opened'); // Para debug
    }
    
    public function closeTableModal()
    {
        $this->showTableModal = false;
        $this->dispatch('table-modal-closed'); // Para debug
    }
    
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
        
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => "{$product->name} adicionado ao carrinho"
        ]);
        
        $this->dispatch('cart-updated');
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
            $this->cart[$cartKey]['quantity'] = $newQuantity;
            $this->cart[$cartKey]['total_price'] = $newQuantity * $this->cart[$cartKey]['unit_price'];
        }
    }
    
    public function clearCart()
    {
        $this->cart = [];
        $this->dispatch('toast', [
            'type' => 'info',
            'message' => 'Carrinho limpo'
        ]);
    }
    
    public function processPayment()
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
        
        // Aqui você implementaria a lógica de pagamento
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Processando pagamento...'
        ]);
        
        // Por enquanto, vamos simular
        $this->cart = [];
        $this->currentTable = null;
    }
    public function render()
    {
        $companyId = auth()->user()->company_id;
        
        // dd(Table::active()->get());
        return view('livewire.p-o-s.p-o-s-component',[
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
            'currentTable' => $this->currentTable,
            'shiftInfo' => '08:00 - 20:00' // Você pode pegar do turno ativo
        ])->layout('layouts.pos');
    }
}
