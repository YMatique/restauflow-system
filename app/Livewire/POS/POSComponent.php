<?php

namespace App\Livewire\POS;

use App\Models\Product;
use App\Models\Table;
use App\Livewire\POS\TablesList;
use App\Models\Category;
use Livewire\Component;

class POSComponent extends Component
{

    
    // Estados da Interface
    public $currentView = 'tables'; // 'tables' ou 'products'
    public $currentTable = null;
    public $selectedCategory = null;
    public $cart = [];
    
    // Stats do dia (podem vir do banco mais tarde)
    public $dailyStats = [
        'activeTables' => 0,
        'ordersToday' => 0,
        'todayRevenue' => 0
    ];

    protected $listeners = [
        'tableSelected' => 'handleTableSelection',
        'productAdded' => 'handleProductAddition',
        'cartItemUpdated' => 'handleCartItemUpdate',
        'cartCleared' => 'handleCartClear',
        'orderFinalized' => 'handleOrderFinalized',
        'viewChanged' => 'handleViewChange'
    ];

    public function mount()
    {
        // Verificar turno ativo
        if (!auth()->user()->getActiveShift()) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'É necessário ter um turno ativo para usar o POS'
            ]);
            return redirect()->route('restaurant.shifts');
        }

        // Carregar stats iniciais
        $this->loadDailyStats();
        
        // Definir categoria inicial
        $this->selectedCategory = Category::active()
            ->byCompany(auth()->user()->company_id)
            ->first()?->id;
    }

    // ===== HANDLERS DE EVENTOS =====

    public function handleTableSelection($tableId)
    {
        $table = Table::find($tableId);
        
        // dd('tableSelected', $table);
        if (!$table) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Mesa não encontrada'
            ]);
            return;
        }

        $this->currentTable = $table;
        
        // Se a mesa já tem pedidos, carregá-los no carrinho
        // (isso seria implementado com um relacionamento no modelo)
        
        $this->dispatch('toast', [
            'type' => 'success', 
            'message' => "Mesa {$table->name} selecionada"
        ]);
    }

    public function handleProductAddition($productId, $quantity = 1)
    {
        if (!$this->currentTable) {
            $this->dispatch('toast', [
                'type' => 'warning',
                'message' => 'Selecione uma mesa primeiro'
            ]);
            return;
        }

        $product = Product::find($productId);
        
        if (!$product || !$product->canSell($quantity)) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Produto indisponível ou sem stock'
            ]);
            return;
        }

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
                'total_price' => $product->price * $quantity,
                'table_id' => $this->currentTable->id
            ];
        }

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => "{$product->name} adicionado ao carrinho"
        ]);
        
        // Atualizar status da mesa se necessário
        if ($this->currentTable->status === 'available') {
            $this->currentTable->update(['status' => 'occupied']);
        }
    }

    public function handleCartItemUpdate($cartKey, $quantity)
    {
        if ($quantity <= 0) {
            unset($this->cart[$cartKey]);
            $this->dispatch('toast', [
                'type' => 'info',
                'message' => 'Item removido do carrinho'
            ]);
        } else {
            if (isset($this->cart[$cartKey])) {
                $this->cart[$cartKey]['quantity'] = $quantity;
                $this->cart[$cartKey]['total_price'] = 
                    $quantity * $this->cart[$cartKey]['unit_price'];
            }
        }
        
        // Se carrinho ficar vazio e mesa só tinha este pedido, marcar como disponível
        if (empty($this->cart) && $this->currentTable->status === 'occupied') {
            $this->currentTable->update(['status' => 'available']);
        }
    }

    public function handleCartClear()
    {
        $this->cart = [];
        
        if ($this->currentTable && $this->currentTable->status === 'occupied') {
            $this->currentTable->update(['status' => 'available']);
        }
        
        $this->dispatch('toast', [
            'type' => 'info',
            'message' => 'Carrinho limpo'
        ]);
    }

    public function handleOrderFinalized($saleId)
    {
        // Reset após venda bem-sucedida
        $this->cart = [];
        $this->currentTable = null;
        $this->currentView = 'tables';
        
        $this->loadDailyStats(); // Atualizar estatísticas
        
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Pedido finalizado com sucesso!'
        ]);
    }

    public function handleViewChange($view)
    {
        $this->currentView = $view;
    }

    // ===== AÇÕES DA INTERFACE =====

    public function showProducts()
    {
        if (!$this->currentTable) {
            $this->dispatch('toast', [
                'type' => 'warning',
                'message' => 'Selecione uma mesa primeiro'
            ]);
            return;
        }
        
        $this->currentView = 'products';
    }

    public function showTables()
    {
        $this->currentView = 'tables';
    }

    public function selectCategory($categoryId)
    {
        $this->selectedCategory = $categoryId;
    }

    public function selectTableQuick($tableId)
    {
        // Para acesso rápido da sidebar
        $this->handleTableSelection($tableId);
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

    // ===== MÉTODOS AUXILIARES =====

    private function loadDailyStats()
    {
        $companyId = auth()->user()->company_id;
        
        $this->dailyStats = [
            'activeTables' => Table::where('company_id', $companyId)
                ->where('status', '!=', 'available')
                ->count(),
            'ordersToday' => 45, // Implementar query real
            'todayRevenue' => 85400 // Implementar query real
        ];
    }

    public function render()
    {
        return view('livewire.p-o-s.p-o-s-component', [
            'cartTotal' => $this->cartTotal,
            'cartCount' => $this->cartCount,
            'activeShift' => $this->activeShift,
        ])->layout('layouts.pos-new');
    }
}
