<?php

namespace App\Livewire\Stock;

use App\Models\Product;
use App\Models\StockMovement;
use Livewire\Component;

class StockManagement extends Component
{
    protected string $layout = 'layouts.app';

    public $activeTab = 'products';
    public $showMovementModal = false;
    public $movementType = 'in'; // 'in' or 'out'

    public $movementForm = [
        'item_type' => '',
        'item_id' => '',
        'quantity' => '',
        'reason' => 'stock_entry',
        'supplier' => '',
        'unit_cost' => '',
        'invoice_number' => '',
        'notes' => ''
    ];

    public function openMovementModal($type = 'in')
    {
        $this->movementType = $type;
        $this->showMovementModal = true;
        $this->reset('movementForm');
    }

    public function saveMovement()
    {
        $this->validate([
            'movementForm.item_type' => 'required|in:product,ingredient',
            'movementForm.item_id' => 'required|exists:products,id',
            'movementForm.quantity' => 'required|numeric|min:0.01',
            'movementForm.unit_cost' => 'nullable|numeric|min:0',
            'movementForm.reason' => 'required|string',
            'movementForm.notes' => 'nullable|string|max:500'
        ]);

        $product = Product::find($this->movementForm['item_id']);

        $movementData = [
            'product_id' => $product->id,
            'type' => $this->movementType,
            'quantity' => $this->movementForm['quantity'],
            'previous_stock' => $product->stock_quantity,
            'reason' => $this->movementForm['reason'],
            'notes' => $this->movementForm['notes'],
            'date' => now(),
            'user_id' => auth()->id(),
            'company_id' => auth()->user()->company_id
        ];

        if ($this->movementType === 'in') {
            $movementData = array_merge($movementData, [
                'supplier' => $this->movementForm['supplier'],
                'unit_cost' => $this->movementForm['unit_cost'],
                'invoice_number' => $this->movementForm['invoice_number']
            ]);

            $product->addStock(
                $this->movementForm['quantity'],
                $this->movementForm['reason'],
                auth()->id(),
                $movementData
            );
        } else {
            $product->reduceStock(
                $this->movementForm['quantity'],
                $this->movementForm['reason'],
                auth()->id()
            );
        }

        $this->showMovementModal = false;
        $this->reset('movementForm');

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Movimento de stock registrado com sucesso!'
        ]);
    }
    public function render()
    {
         $stats = [
            'total_items' => Product::byCompany(auth()->user()->company_id)->active()->count(),
            'low_stock' => Product::byCompany(auth()->user()->company_id)->lowStock()->count(),
            'out_stock' => Product::byCompany(auth()->user()->company_id)->where('stock_quantity', '<=', 0)->count(),
            'total_value' => Product::byCompany(auth()->user()->company_id)
                ->selectRaw('SUM(stock_quantity * price) as total')
                ->value('total') ?? 0
        ];

        $recentMovements = StockMovement::byCompany(auth()->user()->company_id)
            ->with(['product', 'user'])
            ->latest('date')
            ->limit(20)
            ->get();

        $lowStockAlerts = Product::byCompany(auth()->user()->company_id)
            ->lowStock()
            ->with('category')
            ->get();


        return view('livewire.stock.stock-management', [
            'stats' => $stats,
            'recentMovements' => $recentMovements,
            'lowStockAlerts' => $lowStockAlerts,
            'products' => Product::byCompany(auth()->user()->company_id)
                ->active()
                ->with('category')
                ->get(),
                'title' =>  __('messages.stock_management.title'),
                // 'title' =>  __('messages.welcome',  ['name' => 'dayle']),

            'breadcrumb' => 'Dashboard  > Stock'
        ]);
    }
}
