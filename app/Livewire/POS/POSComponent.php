<?php

namespace App\Livewire\POS;

use App\Models\Product;
use App\Models\Table;
use App\Models\Category;
use Livewire\Component;

class POSComponent extends Component
{
    // Estados da interface
    public string $currentView = 'tables'; // 'tables' ou 'products'
    
    // Dados principais
    public ?Table $currentTable = null;
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
        $this->currentTable = Table::find($tableId);
        
        if (!$this->currentTable) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Mesa não encontrada']);
            return;
        }

        $this->currentView = 'products';
        $this->loadProducts();
        
        $this->dispatch('toast', ['type' => 'success', 'message' => "Mesa {$this->currentTable->name} selecionada"]);
    }

    public function backToTables()
    {
        $this->currentView = 'tables';
        $this->currentTable = null;
        $this->selectedCategory = null;
        $this->cart = []; // Opcional: limpar carrinho ao voltar
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
        if (!$this->currentTable) {
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

        if (!$this->currentTable) {
            $this->dispatch('toast', ['type' => 'warning', 'message' => 'Mesa não selecionada']);
            return;
        }

        // Aqui você colocaria a lógica para salvar a venda no banco
        // Por enquanto, apenas simula
        
        $this->cart = [];
        $this->currentTable = null;
        $this->currentView = 'tables';
        
        $this->dispatch('toast', ['type' => 'success', 'message' => 'Pedido finalizado com sucesso!']);
    }

    // ===========================================
    // MÉTODOS DE CARREGAMENTO DE DADOS
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
    
    public function getCartTotalProperty()
    {
        return collect($this->cart)->sum('total_price');
    }

    public function getCartCountProperty()
    {
        return collect($this->cart)->sum('quantity');
    }

    public function getQuickTablesProperty()
    {
        return collect($this->tables)->take(8);
    }

    // ===========================================
    // RENDER
    // ===========================================
    
    public function render()
    {
        return view('livewire.p-o-s.p-o-s-component', [
            'cartTotal' => $this->cartTotal,
            'cartCount' => $this->cartCount,
            'quickTables' => $this->quickTables,
        ])->layout('layouts.pos-new');
    }
}