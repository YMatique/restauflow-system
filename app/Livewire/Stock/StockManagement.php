<?php

namespace App\Livewire\Stock;

use App\Models\Stock;
use App\Models\StockProduct;
use App\Traits\WithToast;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;


#[Layout('components.layouts.app-main')]
class StockManagement extends Component
{
    use WithPagination, WithToast;

    // Pagination
    public $perPage = 10;

    // Filters & Search
    public $statusFilter = '';
    public $search = '';

    // View controllers
    public $showModal = false;
    public $editingStock = false;

    // Stock form
    public $stockForm = [
        'name' => '',
        'notes' => '',
        'status' => ''
    ];

    #[Title('Gestão de Stocks')]
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


        try {
              $stock = Stock::create($data);

            if ($stock->wasRecentlyCreated) {
                
                $this->toastSuccess(
                    __('messages.toast.success.key'),
                    __('messages.toast.success.value', ['verb' => 'create', 'object' => 'stock'])
                );
                $this->resetForm();
            } 

        
        } 
        
        catch (\Exception $e)
        
        {
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

    // ----------------------
    // Render
    // ----------------------
    public function render()
    {
        $companyId = auth()->user()->company_id;

        $stocks = Stock::getFilteredStocks(
            companyId: $companyId,
            search: $this->search,
            status: $this->statusFilter,
            perPage: $this->perPage
        );


        return view('livewire.stock.stock-management', [
            'stocks' => $stocks,
            'title'      => __('messages.stock_management.title'),
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('restaurant.dashboard')],
                ['label' => 'Stocks'] // item atual, sem link
            ],
        ]);
    }

}
