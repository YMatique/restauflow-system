<?php

namespace App\Livewire\Inventory;

use App\Models\Product;
use App\Models\Stock;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\StockProduct;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app-main')]
class InventoryViewer extends Component
{
    public $companyId;

    public $stocks;
    public $selectedStockId;

    public $search;
    public $selectedCategoryId;

    public $inventory;

    public $items = [];

    public function mount(Inventory $inventory)
    {
        $this->companyId = auth()->user()->company_id;
        $this->stocks     = Stock::where('company_id', $this->companyId)->get();
        $this->inventory = $inventory;
    }

    public function updatedSelectedStockId()
    {
        $this->loadItems();
    }

    public function updatedSearch()
    {
        $this->loadItems();
    }

    public function updatedSelectedCategoryId()
    {
        $this->loadItems();
    }

    private function loadItems()
    {
        if (!$this->selectedStockId) {
            $this->items = [];
            return;
        }

        $query = StockProduct::with(['product', 'product.category'])
            ->where('company_id', $this->companyId)
            ->where('stock_id', $this->selectedStockId);

        if ($this->search) {
            $query->whereHas('product', function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('slug', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%");
            });
        }

        if ($this->selectedCategoryId) {
            $query->whereHas('product.categories', function ($q) {
                $q->where('id', $this->selectedCategoryId);
            });
        }

        $this->items = $query->get();
    }

    #[Title('Inventário – Visualização')]
    public function render()
    {
        return view('livewire.inventory.inventory-viewer', [
            'categories' => Category::all(),
            'inventory'  => $this->inventory
        ]);
    }
}
