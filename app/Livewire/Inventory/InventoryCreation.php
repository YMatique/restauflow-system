<?php

namespace App\Livewire\Inventory;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;


#[Layout('components.layouts.app-main')]
class InventoryCreation extends Component
{
    #[Title('Criar - Inventorio')]
    public function render()
    {
        return view('livewire.inventory.inventory-creation');
    }
}
