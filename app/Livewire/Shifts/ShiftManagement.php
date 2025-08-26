<?php

namespace App\Livewire\Shifts;

use App\Models\Shift;
use Livewire\Component;

class ShiftManagement extends Component
{
     protected string $layout = 'layouts.app';
    
    public $activeShift = null;
    public $showOpenModal = false;
    public $showCloseModal = false;
    
    public $openShiftForm = [
        'initial_amount' => 2000,
        'opening_notes' => ''
    ];
    
    public $closeShiftForm = [
        'final_amount' => 0,
        'withdrawals' => 0,
        'closing_notes' => ''
    ];
    
    public function mount()
    {
        $this->activeShift = auth()->user()->getActiveShift();
    }
    
    public function openShift()
    {
        $this->validate([
            'openShiftForm.initial_amount' => 'required|numeric|min:0',
            'openShiftForm.opening_notes' => 'nullable|string|max:500'
        ]);
        
        $this->activeShift = Shift::create([
            'user_id' => auth()->id(),
            'company_id' => auth()->user()->company_id,
            'opened_at' => now(),
            'initial_amount' => $this->openShiftForm['initial_amount'],
            'opening_notes' => $this->openShiftForm['opening_notes'],
            'status' => 'open'
        ]);
        
        $this->showOpenModal = false;
        $this->reset('openShiftForm');
        
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Turno iniciado com sucesso!'
        ]);
    }
    
    public function closeShift()
    {
        $this->validate([
            'closeShiftForm.final_amount' => 'required|numeric|min:0',
            'closeShiftForm.withdrawals' => 'nullable|numeric|min:0',
            'closeShiftForm.closing_notes' => 'nullable|string|max:500'
        ]);
        
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
            'message' => 'Turno fechado com sucesso!'
        ]);
    }
    public function render()
    {
        return view('livewire.shifts.shift-management');
    }
}
