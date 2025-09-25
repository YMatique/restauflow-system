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
    public  $currentTable = null;
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
    // MÉTODO TEMPORÁRIO PARA DEBUG
    // ===========================================
    
    public function debugInfo()
    {
        $info = [
            'currentView' => $this->currentView,
            'currentTable' => $this->currentTable ? $this->currentTable->name : null,
            'selectedCategory' => $this->selectedCategory,
            'products_count' => count($this->products),
            'cart_count' => count($this->cart),
            'cart' => $this->cart
        ];
        
        logger("DEBUG INFO: " . json_encode($info));
        $this->dispatch('toast', ['type' => 'info', 'message' => 'Info enviada para logs']);
    }

     public function forceRefresh()
    {
        // Força uma re-renderização completa
        $this->skipRender = false;
        logger("FORCE REFRESH executado");
    }
    // ===========================================
    // MÉTODOS DE NAVEGAÇÃO
    // ===========================================
    
    public function selectTable($tableId)
    {
        logger("=== SELECT TABLE CHAMADO ===");
        logger("Table ID: {$tableId}");
        
        $this->currentTable = Table::find($tableId);
        
        // dd($this->selectTable());
        if (!$this->currentTable) {
            logger("ERRO: Mesa não encontrada - ID: {$tableId}");
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Mesa não encontrada']);
            return;
        }

        logger("Mesa encontrada: {$this->currentTable->name}");
        $this->currentView = 'products';
        logger("View alterada para: {$this->currentView}");
        
        $this->loadProducts(); // Carrega produtos quando seleciona mesa
        logger("Produtos carregados: " . count($this->products));

        // FORÇA ATUALIZAÇÃO DO COMPONENTE
        $this->dispatch('$refresh');
        logger("=== FIM SELECT TABLE ===");
        
        $this->dispatch('toast', ['type' => 'success', 'message' => "Mesa {$this->currentTable->name} selecionada"]);
    }

    public function backToTables()
    {
        $this->currentView = 'tables';
        $this->currentTable = null;
        $this->selectedCategory = null;
        $this->products = []; // Limpa produtos
        // Opcional: limpar carrinho ao voltar
        // $this->cart = [];
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
            logger("Novo produto adicionado ao carrinho");
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