<?php

namespace App\Livewire\Shifts;

use App\Models\CashMovement;
use App\Models\Shift;
use Livewire\Component;

class ShiftManagement extends Component
{
 public $activeShift = null;
    public $showOpenModal = false;
    public $showCloseModal = false;
    public $showWithdrawalModal = false;
    public $showHistoryModal = false;
    
    public $openShiftForm = [
        'initial_amount' => 2000,
        'opening_notes' => ''
    ];
    
    public $closeShiftForm = [
        'final_amount' => 0,
        'withdrawals' => 0,
        'closing_notes' => ''
    ];
    
    public $withdrawalForm = [
        'amount' => 0,
        'description' => ''
    ];
    
    public function mount()
    {
        $this->activeShift = auth()->user()->getActiveShift();
        
        if ($this->activeShift) {
            $this->closeShiftForm['withdrawals'] = $this->activeShift->withdrawals ?? 0;
        }
    }
    
    public function openShift()
    {
        $this->validate([
            'openShiftForm.initial_amount' => 'required|numeric|min:0',
            'openShiftForm.opening_notes' => 'nullable|string|max:500'
        ]);
        
        try {
            $this->activeShift = Shift::create([
                'user_id' => auth()->id(),
                'company_id' => auth()->user()->company_id,
                'opened_at' => now(),
                'initial_amount' => $this->openShiftForm['initial_amount'],
                'opening_notes' => $this->openShiftForm['opening_notes'],
                'status' => 'open',
                'total_sales' => 0,
                'total_orders' => 0,
                'withdrawals' => 0
            ]);
            
            // Create opening cash movement
            CashMovement::create([
                'shift_id' => $this->activeShift->id,
                'type' => 'opening',
                'amount' => $this->openShiftForm['initial_amount'],
                'description' => "Abertura do turno #{$this->activeShift->id}",
                'category' => 'opening',
                'date' => now(),
                'user_id' => auth()->id(),
                'company_id' => auth()->user()->company_id
            ]);
            
            $this->showOpenModal = false;
            $this->reset('openShiftForm');
            
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Turno iniciado com sucesso! Sistema POS liberado.'
            ]);
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erro ao abrir turno: ' . $e->getMessage()
            ]);
        }
    }
    
    public function closeShift()
    {
        $this->validate([
            'closeShiftForm.final_amount' => 'required|numeric|min:0',
            'closeShiftForm.withdrawals' => 'nullable|numeric|min:0',
            'closeShiftForm.closing_notes' => 'nullable|string|max:500'
        ]);
        
        try {
            $this->activeShift->close(
                $this->closeShiftForm['final_amount'],
                $this->closeShiftForm['closing_notes'],
                $this->closeShiftForm['withdrawals'] ?? 0
            );
            
            $this->activeShift = null;
            $this->showCloseModal = false;
            $this->reset('closeShiftForm');
            
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Turno fechado com sucesso! Sistema bloqueado.'
            ]);
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erro ao fechar turno: ' . $e->getMessage()
            ]);
        }
    }
    
    public function addWithdrawal()
    {
        $this->validate([
            'withdrawalForm.amount' => 'required|numeric|min:0.01',
            'withdrawalForm.description' => 'required|string|min:3|max:255'
        ]);
        
        try {
            $this->activeShift->addWithdrawal(
                $this->withdrawalForm['amount'],
                $this->withdrawalForm['description'],
                auth()->id()
            );
            
            // Update local form data
            $this->closeShiftForm['withdrawals'] = $this->activeShift->fresh()->withdrawals;
            
            $this->showWithdrawalModal = false;
            $this->reset('withdrawalForm');
            
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Retirada registrada com sucesso!'
            ]);
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erro ao registrar retirada: ' . $e->getMessage()
            ]);
        }
    }
    
    public function updatedCloseShiftFormFinalAmount()
    {
        // Auto-calculate expected values when final amount changes
        $this->dispatch('recalculate-difference');
    }
    
    public function getShiftStats()
    {
        if (!$this->activeShift) {
            return [];
        }
        
        return $this->activeShift->getStats();
    }
    
    public function exportShiftReport()
    {
        if (!$this->activeShift) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Nenhum turno ativo para exportar'
            ]);
            return;
        }
        
        // This would generate a PDF report
        $this->dispatch('toast', [
            'type' => 'info',
            'message' => 'Funcionalidade de exportação em desenvolvimento'
        ]);
    }
    
    public function refreshData()
    {
        $this->activeShift = $this->activeShift ? $this->activeShift->fresh() : null;
        
        if ($this->activeShift) {
            $this->closeShiftForm['withdrawals'] = $this->activeShift->withdrawals ?? 0;
        }
    }
    public function render()
    {
        // Refresh shift data periodically
        if ($this->activeShift) {
            $this->activeShift = $this->activeShift->fresh();
        }
        return view('livewire.shifts.shift-management', [
            'title' => 'Gestão de Turnos',
            'breadcrumb' => 'Dashboard > Turnos',
            'shiftStats' => $this->getShiftStats()
        ])->layout('layouts.shift-layout');//->layout('layouts.app');
    }
}
