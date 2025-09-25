<?php

namespace App\Livewire\POS;

use App\Models\Product;
use App\Models\Table;
use App\Livewire\POS\TablesList;
use App\Models\Category;
use Livewire\Component;

class POSComponent extends Component
{

// Define se a view principal é 'tables' ou 'products'
    public string $currentView = 'tables'; 

    // A mesa selecionada, passada para OrderSummary
    public ?Table $currentTable = null; 

    protected $listeners = [
        // Ouve a seleção das grids de mesa
        'tableSelected' => 'handleTableSelection', 
        
        // Ouve o clique no botão 'Adicionar Produtos' do OrderSummary
        'showProducts' => 'showProducts',
        
        // Ouve quando o OrderSummary zera o carrinho e precisa liberar a mesa
        'cartEmptied' => 'handleCartEmptied', 
        
        // Ouve quando o pagamento é finalizado
        'orderFinalized' => 'handleOrderFinalized', 
    ];

    public function handleTableSelection(int $tableId): void
    {
        $this->currentTable = Table::find($tableId);
        
        if ($this->currentTable) {
            $this->currentView = 'products';
            // Se a mesa estava disponível, pode ser marcada como ocupada aqui, ou na finalização do pedido.
            // Para simplificar, assumimos que o status é atualizado na finalização.
        } else {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Mesa não encontrada.']);
        }
    }

    public function showProducts(): void
    {
        $this->currentView = 'products';
    }

    public function showTables(): void
    {
        $this->currentView = 'tables';
    }

    public function handleCartEmptied(int $tableId): void
    {
        // Ao limpar o carrinho, atualiza o status da mesa para "available"
        $table = Table::find($tableId);
        if ($table && $table->status === 'occupied') { 
            $table->update(['status' => 'available']);
            $this->dispatch('refreshTables'); // Avisa as grids de mesa para atualizar
        }
    }

    public function handleOrderFinalized(): void
    {
        // Lógica de reset total após uma venda
        $this->currentTable = null;
        $this->currentView = 'tables';
        $this->dispatch('refreshTables');
        $this->dispatch('toast', ['type' => 'success', 'message' => 'Pedido finalizado com sucesso!']);
    }

    public function render()
    {
         $companyId = auth()->user()->company_id;
        return view('livewire.p-o-s.p-o-s-component', [
            'cartTotal' => 0,
            'cartCount' => 0,
            'activeShift' => 0,
        ])->layout('layouts.pos-new');
    }
}
