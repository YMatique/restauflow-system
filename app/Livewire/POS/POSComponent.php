<?php

namespace App\Livewire\POS;

use App\Models\Product;
use App\Models\Table;
use App\Models\Category;
use Livewire\Component;

class POSComponent extends Component
{
    // Estados da interface
    public string $currentView = 'tables';
    
    // Dados principais - propriedades simples
    public ?int $currentTableId = null;
    public ?string $currentTableName = null;
    public ?int $selectedCategory = null;
    public array $cart = [];
    
    // Dados para templates
    public $tables = [];
    public $categories = [];
    public $products = [];

    public function mount()
    {
        $this->loadTables();
        $this->loadCategories();
    }

    // ===========================================
    // MÉTODOS DE NAVEGAÇÃO
    // ===========================================
    
    public function selectTable($tableId)
    {
        $table = Table::find($tableId);
        
        if (!$table) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Mesa não encontrada']);
            return;
        }

        $this->currentTable = $table;
        $this->currentTableId = $table->id;
        $this->currentTableName = $table->name;
        $this->currentView = 'products';
        
        $this->loadProducts();
        
        $this->dispatch('toast', ['type' => 'success', 'message' => "Mesa {$table->name} selecionada"]);
    }

    public function backToTables()
    {
        $this->currentView = 'tables';
        $this->currentTableId = null;
        $this->currentTableName = null;
        $this->selectedCategory = null;
        $this->products = [];
    }

    // ===========================================
    // MÉTODOS DE CATEGORIA
    // ===========================================
    
    public function selectCategory($categoryId = null)
    {
        $this->selectedCategory = $categoryId;
        $this->loadProducts();
    }

    // ===========================================
    // MÉTODOS DO CARRINHO
    // ===========================================
    
    public function addToCart($productId, $quantity = 1)
    {
        if (!$this->currentTableId) {
            $this->dispatch('toast', ['type' => 'warning', 'message' => 'Selecione uma mesa primeiro']);
            return;
        }

        $product = Product::find($productId);
        if (!$product) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Produto não encontrado']);
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

        $this->dispatch('toast', ['type' => 'success', 'message' => "{$product->name} adicionado ao carrinho"]);
    }

    public function updateQuantity($cartKey, $newQuantity)
    {
        if ($newQuantity <= 0) {
            unset($this->cart[$cartKey]);
            $this->dispatch('toast', ['type' => 'info', 'message' => 'Item removido']);
        } else {
            $this->cart[$cartKey]['quantity'] = $newQuantity;
            $this->cart[$cartKey]['total_price'] = $newQuantity * $this->cart[$cartKey]['unit_price'];
        }
    }

    public function removeFromCart($cartKey)
    {
        unset($this->cart[$cartKey]);
        $this->dispatch('toast', ['type' => 'info', 'message' => 'Item removido do carrinho']);
    }

    public function clearCart()
    {
        $this->cart = [];
        $this->dispatch('toast', ['type' => 'info', 'message' => 'Carrinho limpo']);
    }

    // ===========================================
    // MÉTODOS DE FINALIZAÇÃO
    // ===========================================
    
    public function finalizeOrder()
    {
        if (empty($this->cart)) {
            $this->dispatch('toast', ['type' => 'warning', 'message' => 'Carrinho vazio']);
            return;
        }

        if (!$this->currentTableId) {
            $this->dispatch('toast', ['type' => 'warning', 'message' => 'Mesa não selecionada']);
            return;
        }

        // Aqui colocaria a lógica para salvar no banco
        
        $this->cart = [];
        $this->currentTableId = null;
        $this->currentTableName = null;
        $this->currentView = 'tables';
        
        $this->dispatch('toast', ['type' => 'success', 'message' => 'Pedido finalizado com sucesso!']);
    }

    // ===========================================
    // MÉTODOS DE CARREGAMENTO
    // ===========================================
    
    public function loadTables()
    {
        $this->tables = Table::where('company_id', auth()->user()->company_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function loadCategories()
    {
        $this->categories = Category::where('company_id', auth()->user()->company_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function loadProducts()
    {
        $query = Product::where('company_id', auth()->user()->company_id)
            ->where('is_active', true)
            ->where('is_available', true);

        if ($this->selectedCategory) {
            $query->where('category_id', $this->selectedCategory);
        }

        $this->products = $query->orderBy('name')->get();
    }

    // ===========================================
    // PROPRIEDADES COMPUTADAS
    // ===========================================
    
    public function getCurrentTableProperty()
    {
        if ($this->currentTableId) {
            return (object) [
                'id' => $this->currentTableId,
                'name' => $this->currentTableName
            ];
        }
        return null;
    }

    public function getCartTotalProperty()
    {
        return collect($this->cart)->sum('total_price');
    }

    public function getCartCountProperty()
    {
        return collect($this->cart)->sum('quantity');
    }

    // ===========================================
    // RENDER
    // ===========================================
    
    public function render()
    {      
        return view('livewire.p-o-s.p-o-s-component', [
            'cartTotal' => $this->cartTotal,
            'cartCount' => $this->cartCount,
            // 'quickTables' => $this->quickTables,


            
            // CORREÇÃO: Passar todas as variáveis necessárias explicitamente
            'currentView' => $this->currentView,
            'currentTable' => $this->currentTable,
            'selectedCategory' => $this->selectedCategory,
            'cart' => $this->cart,
            'tables' => $this->tables,
            'categories' => $this->categories,
            'products' => $this->products,
        ])->layout('layouts.pos-new');
    }
}