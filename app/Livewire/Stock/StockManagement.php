<?php

namespace App\Livewire\Stock;

use App\Models\Stock;
use App\Models\StockProduct;
use App\Traits\WithToast;
use Livewire\Component;
use Livewire\WithPagination;

class StockManagement extends Component
{
    use WithPagination, WithToast;

    protected string $layout = 'layouts.app';

    // Pagination
    public $perPage = 10;

    // Filters & Search
    public $statusFilter = '';
    public $search = '';

    // Status dropdown
    public $statusDropDown = [];

    // View controllers
    public $currentView = 'stock';
    public $showModal = false;
    public $editingStock = false;
    public $showDetailedStockMode = false;
    public $selectedStockId = null;

    // Stock form
    public $stockForm = [
        'name' => '',
        'notes' => '',
        'status' => ''
    ];

    public function mount()
    {
        $this->statusDropDown = [
            'active' => __('messages.status.active'),
            'inactive' => __('messages.status.inactive'),
            'maintenance' => __('messages.status.maintenance'),
        ];
    }

    // Validation rules
    protected $rules = [
        'stockForm.name' => 'required|string|max:255',
        'stockForm.notes' => 'nullable|string',
        'stockForm.status' => 'required|in:active,inactive,maintenance',
    ];

    // ----------------------
    // Modal & Form methods
    // ----------------------
    public function createStock()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function editStock(Stock $stock)
    {
        $this->stockForm = collect($stock->attributesToArray())
            ->except(['created_at', 'updated_at'])
            ->toArray();
        $this->editingStock = true;
        $this->showModal = true;
    }

    public function resetForm()
    {
        $this->reset(['stockForm', 'editingStock', 'showModal']);
    }

    // ----------------------
    // CRUD methods
    // ----------------------
    public function saveStock()
    {
        $this->validate();

        $companyId = auth()->user()->company_id;

        $data = array_merge(['company_id' => $companyId], $this->stockForm);

        if (Stock::create($data)) {
            $this->toastSuccess(
                __('messages.toast.success.key'),
                __('messages.toast.success.value', ['verb' => 'create', 'object' => 'stock'])
            );
            $this->resetForm();
        } else {
            $this->toastError(
                __('messages.toast.error.key'),
                __('messages.toast.error.value', ['verb' => 'create', 'object' => 'stock'])
            );
        }
    }

    public function updateStock(Stock $stock)
    {
        $this->validate();
        $stock->update($this->stockForm);

        $this->toastSuccess(
            __('messages.toast.success.key'),
            __('messages.toast.success.value', ['verb' => 'update', 'object' => 'stock'])
        );

        $this->resetForm();
    }

    public function deleteStock(Stock $stock)
    {
        if ($stock->availableProducts()->doesntExist()) {

            $stock->delete();

             $this->toastSuccess(
                __('messages.toast.success.key'),
                __('messages.toast.success.value', ['verb' => 'delete', 'object' => 'stock'])
            );
        }

        $this->toastError(
            __('messages.toast.error.key'),
            __('messages.toast.error.value', ['verb' => 'delete', 'object' => 'stock'])
        );
    }

    public function showDetailedStock(Stock $stock)
    {
        $this->showDetailedStockMode = true;
        $this->currentView = 'detail';
        $this->selectedStockId = $stock->id;
    }

    // ----------------------
    // Render
    // ----------------------
    public function render()
    {
        $companyId = auth()->user()->company_id;
        $config = $this->getViewConfig($this->currentView);

        switch ($config['items']) {
            case 'stocks':
                $items = Stock::getFilteredStocks(
                    companyId: $companyId,
                    search: $this->search,
                    status: $this->statusFilter,
                    perPage: $this->perPage
                );
                break;

            case 'products':

                $items = Stock::getProductsSummary(
                    companyId: $companyId,
                    perPage:  $this->perPage,
                    stockId: $this->selectedStockId, // opcional - caso contrario leva tudo
                );

                break;

            default:
                $items = collect();
        }

        return view($config['view'], [
            $config['items'] => $items,
            'title' => $config['title'],
            'breadcrumb' => $config['breadcrumb'],
        ]);
    }
    private function getViewConfig(string $view): array
    {
        return match ($view) {
            'stock' => [
                'view'       => 'livewire.stock.stock-management',
                'title'      => __('messages.stock_management.title'),
                'items'      => 'stocks',
                 'breadcrumb' => [
                                    ['label' => 'Dashboard', 'url' => route('restaurant.dashboard')],
                                    ['label' => 'Stocks'] // item atual, sem link
                ],
            ],
            'detail' => [
                'view'       => 'livewire.stock.stock-show',
                'title'      => __('messages.stock_management.stock_detail'),
                'items'      => 'products',
                'breadcrumb' => [
                                    ['label' => 'Dashboard', 'url' => route('restaurant.dashboard')],
                                    ['label' => 'Stocks', 'url' => route('restaurant.stocks')],
                                    ['label' => 'Details'] // item atual, sem link
                ],

            ],
            default => [
                'view'       => 'livewire.stock.stock-management',
                'title'      => __('messages.stock_management.title'),
                'items'      => 'stocks',
                'breadcrumb' => [
                                    ['label' => 'Dashboard', 'url' => route('restaurant.dashboard')],
                                    ['label' => 'Stocks'] // item atual, sem link
                ],
            ]
        };
    }


}
