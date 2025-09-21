<?php

namespace App\Livewire\POS;

use App\Models\Table;
use Livewire\Component;

class TablesList extends Component
{

    public $currentTableId = null;
    
    protected $listeners = [
        'tableCleared' => '$refresh'
    ];

    public function selectTable($tableId)
    {
        $table = Table::find($tableId);
        
        if (!$table) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Mesa não encontrada'
            ]);
            return;
        }

        if (!$table->isAvailable()) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Mesa não disponível'
            ]);
            return;
        }

        $this->currentTableId = $tableId;
        
        // Envia evento para componente pai
        $this->dispatch('tableSelected', tableId: $tableId);
        
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => "Mesa {$table->name} selecionada"
        ]);
    }

    public function getTablesProperty()
    {
        return Table::active()
            ->byCompany(auth()->user()->company_id)
            ->get()
            ->groupBy('section');
    }

    public function render()
    {
        return view('livewire.p-o-s.tables-list',[
            'tablesBySection' => $this->tables
        ]);
    }
}
