<?php

namespace App\Livewire\POS;

use App\Models\Table;
use Livewire\Component;

class TablesGrid extends Component
{
    protected $listeners = [
        'tableUpdated' => '$refresh',
        'refreshTables' => '$refresh'
    ];

    public $table;
    public function selectTable($tableId)
    {
        $this->table = Table::find($tableId);
        
        if (!$this->table) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Mesa não encontrada'
            ]);
            return;
        }


        // Disparar evento para o componente pai
        $this->dispatch('tableSelected', tableId: $tableId);
    }

    public function getTablesProperty()
    {
        return Table::active()
            ->byCompany(auth()->user()->company_id)
            ->orderBy('name')
            ->get();
    }
    public function render()
    {
        return view('livewire.p-o-s.tables-grid', [
            'tables' => $this->tables
        ]);
    }
}
