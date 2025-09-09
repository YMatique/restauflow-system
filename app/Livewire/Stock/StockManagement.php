<?php

namespace App\Livewire\Stock;

use App\Models\Stock;
use App\Traits\WithToast;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

// #[Layout('components.layouts.app-main')]

class StockManagement extends Component
{
    use WithPagination, WithToast;

    protected string $layout = 'layouts.app';

    //PAGination
    public $perPage = 10;

    //Filters and seacher
    public $statusFilter = '';

    public $search = '';

    public $showModal = false;


    public $editingStock  = false;

    //Select
    public $statusDropDown = [];

    //StockForm
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



    public function createStock(){
        $this->resetForm();
        $this->showModal = true;
    }

    public function resetForm(){
        $this->reset(['stockForm']);
        $this->showModal = false;
    }


    public function editStock(Stock $stock){
        $this->stockForm = collect($stock->attributesToArray())
            ->except(['created_at', 'updated_at'])
            ->toArray();
        $this->editingStock = true;
        $this->showModal = true;
    }

    public function updateStock(Stock $stock){
        $this->validate();
        $stock->update($this->stockForm);
        $this->resetForm();
    }

    public function deleteStock(Stock $stock){
        $stock->delete();
    }

    public function saveStock(){

        $this->validate();

        $companyId = auth()->user()->company_id;

        $this->stockForm = array_merge(['company_id' => $companyId], $this->stockForm);

        if(Stock::create($this->stockForm)){
            $this->toastSuccess('');

            $this->reset(['stockForm', 'showModal']);

            $this->toastError(
                __('messages.toast.success.key'),
                __('messages.toast.success.value', ['verb' => 'create', 'object' => 'stock'])
            );
        }

        else {
            $this->toastError(
                __('messages.toast.error.key'),
                __('messages.toast.error.value', ['verb' => 'create', 'object' => 'stock'])
            );
        }
    }



    public function render()
    {
       $companyId = auth()->user()->company_id;

        $stocks = Stock::query()
            ->where('company_id', $companyId)
            ->when($this->search, fn($q) =>
                $q->where('name', 'like', "%{$this->search}%")
            )
            ->when($this->statusFilter, fn($q) =>
                $q->where('status', $this->statusFilter)
            )
            ->paginate($this->perPage);

        return view('livewire.stock.stock-management', [
            'stocks'            => $stocks,
            'title'             => __('messages.stock_management.title'),
            'breadcrumb'        => 'Dashboard > Stock'
        ]);
    }
}
