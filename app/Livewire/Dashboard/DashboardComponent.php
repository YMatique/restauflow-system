<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;

class DashboardComponent extends Component
{
     protected string $layout = 'layouts.app';
    public function render()
    {
        return view('livewire.dashboard.dashboard-component', [
            'title' => 'Dashboard Principal',
            'breadcrumb' => 'Dashboard'
        ])->layout('layouts.app');
    }
}
