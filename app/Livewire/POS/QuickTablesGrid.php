<?php

namespace App\Livewire\POS;

use App\Models\Table;
use Livewire\Component;

class QuickTablesGrid extends Component
{
    protected $listeners = ['refreshTables' => '$refresh'];
    public $quickTables;

    public function selectTable($tableId)
    {
        // Disparar evento para o componente principal
        $this->dispatch('tableSelected', tableId: $tableId);
    }

    public function getQuickTablesProperty()
    {
        // Pega apenas as primeiras 8 mesas para acesso rápido
        $this->quickTables = Table::active()
            ->byCompany(auth()->user()->company_id)
            ->orderBy('name')
            ->limit(8)
            ->get();
        return $this->quickTables;
    }

    public function mount()
    {
        $this->getQuickTablesProperty();
    }
    public function render()
    {
        return view('livewire.p-o-s.quick-tables-grid',[
            'quickTables' => $this->quickTables
        ]);
    }
}
