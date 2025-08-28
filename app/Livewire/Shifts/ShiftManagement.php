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
    
    protected $listeners = [
        'refreshShiftData' => 'refreshData',
        'shiftOpened' => 'handleShiftOpened',
        'shiftClosed' => 'handleShiftClosed'
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
            'openShiftForm.initial_amount' => 'required|numeric|min:0|max:999999',
            'openShiftForm.opening_notes' => 'nullable|string|max:500'
        ], [
            'openShiftForm.initial_amount.required' => 'O valor inicial é obrigatório',
            'openShiftForm.initial_amount.numeric' => 'O valor deve ser numérico',
            'openShiftForm.initial_amount.min' => 'O valor deve ser maior que zero',
            'openShiftForm.initial_amount.max' => 'O valor é muito alto',
        ]);
        
        try {
            // Verificar se já existe turno ativo
            $existingShift = auth()->user()->getActiveShift();
            if ($existingShift) {
                $this->dispatch('toast', [
                    'type' => 'warning',
                    'message' => 'Já existe um turno ativo!'
                ]);
                return;
            }
            
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
            $this->openShiftForm['initial_amount'] = 2000; // Reset to default
            
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Turno iniciado com sucesso! Sistema POS liberado.',
                'duration' => 5000
            ]);
            
            // Emit event for other components
            $this->dispatch('shiftOpened', ['shiftId' => $this->activeShift->id]);
            
        } catch (\Exception $e) {
            \Log::error('Erro ao abrir turno: ' . $e->getMessage());
            
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erro ao abrir turno. Tente novamente.',
                'duration' => 6000
            ]);
        }
    }
    
    public function closeShift()
    {
        $this->validate([
            'closeShiftForm.final_amount' => 'required|numeric|min:0',
            'closeShiftForm.withdrawals' => 'nullable|numeric|min:0',
            'closeShiftForm.closing_notes' => 'nullable|string|max:500'
        ], [
            'closeShiftForm.final_amount.required' => 'O valor final é obrigatório',
            'closeShiftForm.final_amount.numeric' => 'O valor deve ser numérico',
            'closeShiftForm.final_amount.min' => 'O valor deve ser positivo',
        ]);
        
        try {
            if (!$this->activeShift) {
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => 'Nenhum turno ativo encontrado!'
                ]);
                return;
            }
            
            // Calculate expected amount
            $expectedAmount = ($this->activeShift->initial_amount + 
                             ($this->activeShift->getSalesByPaymentMethod()['cash'] ?? 0)) - 
                             ($this->closeShiftForm['withdrawals'] ?? 0);
            
            $difference = $this->closeShiftForm['final_amount'] - $expectedAmount;
            
            $this->activeShift->update([
                'closed_at' => now(),
                'final_amount' => $this->closeShiftForm['final_amount'],
                'closing_notes' => $this->closeShiftForm['closing_notes'],
                'withdrawals' => $this->closeShiftForm['withdrawals'] ?? 0,
                'difference' => $difference,
                'status' => 'closed'
            ]);
            
            // Create closing cash movement
            CashMovement::create([
                'shift_id' => $this->activeShift->id,
                'type' => 'closing',
                'amount' => $this->closeShiftForm['final_amount'],
                'description' => "Fechamento do turno #{$this->activeShift->id}" . 
                               ($difference != 0 ? " (Diferença: " . number_format($difference, 2) . " MT)" : ""),
                'category' => 'closing',
                'date' => now(),
                'user_id' => auth()->id(),
                'company_id' => auth()->user()->company_id
            ]);
            
            $shiftId = $this->activeShift->id;
            $this->activeShift = null;
            $this->showCloseModal = false;
            $this->reset('closeShiftForm');
            
            $message = 'Turno fechado com sucesso!';
            if ($difference > 0) {
                $message .= ' Sobra de ' . number_format($difference, 0) . ' MT detectada.';
            } elseif ($difference < 0) {
                $message .= ' Falta de ' . number_format(abs($difference), 0) . ' MT detectada.';
            }
            
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => $message,
                'duration' => 6000
            ]);
            
            // Emit event for other components
            $this->dispatch('shiftClosed', ['shiftId' => $shiftId]);
            
        } catch (\Exception $e) {
            \Log::error('Erro ao fechar turno: ' . $e->getMessage());
            
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erro ao fechar turno. Tente novamente.',
                'duration' => 6000
            ]);
        }
    }
    
    public function addWithdrawal()
    {
        $this->validate([
            'withdrawalForm.amount' => 'required|numeric|min:0.01|max:50000',
            'withdrawalForm.description' => 'required|string|min:3|max:255'
        ], [
            'withdrawalForm.amount.required' => 'O valor da retirada é obrigatório',
            'withdrawalForm.amount.numeric' => 'O valor deve ser numérico',
            'withdrawalForm.amount.min' => 'O valor mínimo é 0.01 MT',
            'withdrawalForm.amount.max' => 'O valor máximo é 50,000 MT',
            'withdrawalForm.description.required' => 'A descrição é obrigatória',
            'withdrawalForm.description.min' => 'A descrição deve ter pelo menos 3 caracteres',
        ]);
        
        try {
            if (!$this->activeShift) {
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => 'Nenhum turno ativo encontrado!'
                ]);
                return;
            }
            
            // Create withdrawal cash movement
            CashMovement::create([
                'shift_id' => $this->activeShift->id,
                'type' => 'withdrawal',
                'amount' => -$this->withdrawalForm['amount'], // Negative for withdrawal
                'description' => $this->withdrawalForm['description'],
                'category' => 'withdrawal',
                'date' => now(),
                'user_id' => auth()->id(),
                'company_id' => auth()->user()->company_id
            ]);
            
            // Update shift withdrawals
            $this->activeShift->increment('withdrawals', $this->withdrawalForm['amount']);
            
            // Refresh data
            $this->activeShift = $this->activeShift->fresh();
            $this->closeShiftForm['withdrawals'] = $this->activeShift->withdrawals;
            
            $this->showWithdrawalModal = false;
            $this->reset('withdrawalForm');
            
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Retirada de ' . number_format($this->withdrawalForm['amount'] ?? 0, 0) . ' MT registrada!',
                'duration' => 4000
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Erro ao registrar retirada: ' . $e->getMessage());
            
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erro ao registrar retirada. Tente novamente.',
                'duration' => 5000
            ]);
        }
    }
    
    public function updatedCloseShiftFormFinalAmount()
    {
        // Auto-calculate expected values when final amount changes
        if ($this->activeShift) {
            $this->dispatch('recalculate-difference');
        }
    }
    
    public function setQuickAmount($amount)
    {
        $this->openShiftForm['initial_amount'] = $amount;
    }
    
    public function getShiftStats()
    {
        if (!$this->activeShift) {
            return [];
        }
        
        $stats = [
            'duration' => $this->activeShift->getDurationFormatted(),
            'total_sales' => $this->activeShift->total_sales ?? 0,
            'total_orders' => $this->activeShift->total_orders ?? 0,
            'average_ticket' => $this->activeShift->total_orders > 0 
                ? $this->activeShift->total_sales / $this->activeShift->total_orders 
                : 0,
            'sales_per_hour' => $this->activeShift->getDurationInMinutes() > 0 
                ? ($this->activeShift->total_sales / $this->activeShift->getDurationInMinutes()) * 60 
                : 0
        ];
        
        return $stats;
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
        
        $this->dispatch('toast', [
            'type' => 'info',
            'message' => 'Dados atualizados',
            'duration' => 2000
        ]);
    }
    
    public function handleShiftOpened($data)
    {
        $this->refreshData();
    }
    
    public function handleShiftClosed($data)
    {
        $this->refreshData();
    }
    
    public function getLastShiftProperty()
    {
        return auth()->user()->shifts()
            ->where('status', 'closed')
            ->latest('closed_at')
            ->first();
    }
    
    public function getRecentSalesProperty()
    {
        if (!$this->activeShift) {
            return collect();
        }
        
        return $this->activeShift->sales()
            ->with(['table'])
            ->latest('sold_at')
            ->limit(5)
            ->get();
    }
    
    public function getCashSummaryProperty()
    {
        if (!$this->activeShift) {
            return [];
        }
        
        $cashSales = $this->activeShift->getSalesByPaymentMethod()['cash'] ?? 0;
        $expected = $this->activeShift->initial_amount + $cashSales - $this->activeShift->withdrawals;
        
        return [
            'initial' => $this->activeShift->initial_amount,
            'cash_sales' => $cashSales,
            'withdrawals' => $this->activeShift->withdrawals,
            'expected' => $expected,
            'current' => $this->closeShiftForm['final_amount'] ?? 0,
            'difference' => ($this->closeShiftForm['final_amount'] ?? 0) - $expected
        ];
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
            'shiftStats' => $this->getShiftStats(),
            'lastShift' => $this->lastShift,
            'recentSales' => $this->recentSales,
            'cashSummary' => $this->cashSummary
        ])->layout('layouts.app');
    }
}
