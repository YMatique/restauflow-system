<?php

namespace App\Livewire;

use Livewire\Component;

class PosSystemTest extends Component
{
     public $cart = [];
    public $selectedCategory = 'all';
    public function render()
    {
        return view('livewire.pos-system-test') ->layout('layouts.company', [
                'title' => 'Sistema POS',
                'subtitle' => 'Terminal de Vendas',
                'context' => 'Mesa #3 • Terminal A1',
                'showSidebar' => true,
                'showStockAlerts' => true,
                'sidebarTitle' => 'Categorias',
                'contentClasses' => 'p-6',
            ]);
    }
}
