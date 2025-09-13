<?php

namespace App\Livewire\Restflow;

use App\Models\Product;
use App\Models\StockMovement;
use Livewire\Attributes\Layout;
use Livewire\Component;
#[Layout('components.layouts.app-main')]
class Dashboard extends Component
{
    protected string $layout = 'layouts.app';

    public $activeTab = 'dashboard';

    public function render()
    {
        return view('livewire.restflow.dashboard', [
            'breadcrumb' => 'Dashboard'
        ]);
    }
}

