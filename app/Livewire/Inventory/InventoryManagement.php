<?php

namespace App\Livewire\Inventory;

use App\Models\StockMovement;
use App\Traits\WithToast;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app-main')]
class InventoryManagement extends Component
{
    use WithPagination, WithToast;

    // Pagination
    public $perPage = 10;

    // Filters & Search
    public $typesFilter = '';
    public $search = ''; 

    // View controllers
    public $showModal = false;
    public $editingStock = false;


    #[Title('Gestão de Inventorios')]
    public function render()
    {

        $inventories = StockMovement::byCompany(auth()->user()->company_id)
            ->paginate($this->perPage);


        return view('livewire.inventory.inventory-management', [
            'inventories'   => $inventories,
            'title'         => __('messages.inventory_management.title'),
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('restaurant.dashboard')],
                ['label' =>  __('messages.inventory_management.key') ] // item atual, sem link
            ],

        ]);
    }
}
