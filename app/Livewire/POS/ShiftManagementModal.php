<?php

namespace App\Livewire\POS;

use Livewire\Component;

class ShiftManagementModal extends Component
{
        public $showWithdrawal = false;
    public $showCloseShift = false;

    // Withdrawal Form
    public $withdrawalForm = [
        'amount' => 0,
        'description' => ''
    ];

    // Close Shift Form
    public $closeShiftForm = [
        'final_amount' => 0,
        'closing_notes' => ''
    ];

    public function openWithdrawalModal()
    {
        $this->showWithdrawal = true;
        $this->reset('withdrawalForm');
    }

    public function closeWithdrawalModal()
    {
        $this->showWithdrawal = false;
        $this->reset('withdrawalForm');
    }

    public function registerWithdrawal()
    {
        $this->validate([
            'withdrawalForm.amount' => 'required|numeric|min:0.01',
            'withdrawalForm.description' => 'required|string|min:3|max:255'
        ]);

        $activeShift = auth()->user()->getActiveShift();

        try {
            CashMovement::create([
                'shift_id' => $activeShift->id,
                'type' => 'out',
                'amount' => $this->withdrawalForm['amount'],
                'description' => $this->withdrawalForm['description'],
                'category' => 'withdrawal',
                'date' => now(),
                'user_id' => auth()->id(),
                'company_id' => auth()->user()->company_id
            ]);

            $activeShift->increment('withdrawals', $this->withdrawalForm['amount']);

            $this->closeWithdrawalModal();

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

    public function openCloseShiftModal()
    {
        $activeShift = auth()->user()->getActiveShift();

        try {
            // Calculate expected amount
            $expectedAmount = $activeShift->initial_amount +
                ($activeShift->getSalesByPaymentMethod()['cash'] ?? 0) -
                ($activeShift->withdrawals ?? 0);

            $this->closeShiftForm['final_amount'] = $expectedAmount;
            $this->showCloseShift = true;
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erro ao abrir fechamento: ' . $e->getMessage()
            ]);
        }
    }

    public function closeCloseShiftModal()
    {
        $this->showCloseShift = false;
        $this->reset('closeShiftForm');
    }

    public function closeShift()
    {
        $this->validate([
            'closeShiftForm.final_amount' => 'required|numeric|min:0',
            'closeShiftForm.closing_notes' => 'nullable|string|max:500'
        ]);

        $activeShift = auth()->user()->getActiveShift();

        try {
            $activeShift->close(
                $this->closeShiftForm['final_amount'],
                $this->closeShiftForm['closing_notes'],
                $activeShift->withdrawals ?? 0
            );

            $this->closeCloseShiftModal();

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Turno fechado com sucesso!'
            ]);

            // Notifica componente pai
            $this->dispatch('shiftClosed');
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erro ao fechar turno: ' . $e->getMessage()
            ]);
        }
    }

    public function getCurrentCashBalance()
    {
        $activeShift = auth()->user()->getActiveShift();
        if (!$activeShift) return 0;

        return $activeShift->initial_amount +
            ($activeShift->getSalesByPaymentMethod()['cash'] ?? 0) -
            ($activeShift->withdrawals ?? 0);
    }

    public function getRecentCashMovements()
    {
        $activeShift = auth()->user()->getActiveShift();
        if (!$activeShift) return collect();

        return $activeShift->cashMovements()
            ->latest()
            ->limit(5)
            ->get();
    }
    public function render()
    {
        return view('livewire.p-o-s.shift-management-modal',[
            'activeShift' => auth()->user()->getActiveShift(),
            'currentCashBalance' => $this->getCurrentCashBalance(),
            'recentMovements' => $this->getRecentCashMovements()
        ]);
    }
}
