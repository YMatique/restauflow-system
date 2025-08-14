<?php

namespace App\Livewire\System;

use App\Models\System\Company;
use App\Models\System\Plan;
use App\Models\System\Subscription;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class SubscriptionManagement extends Component
{
    use WithPagination;

    public $showModal = false;
    public $editingId = null;
    public $showCancelModal = false;
    public $cancelId = null;
    public $cancelReason = '';

    // Form fields
    public $company_id = '';
    public $plan_id = '';
    public $starts_at = '';
    public $ends_at = '';
    public $billing_cycle = 'monthly';
    public $amount_paid_mzn = '';
    public $amount_paid_usd = '';
    public $payment_currency = 'MZN';
    public $notes = '';

    // Search and filters
    public $search = '';
    public $statusFilter = '';
    public $planFilter = '';
    public $companyFilter = '';
    public $expiringFilter = '';
    public $perPage = 10;

    protected $queryString = ['search', 'statusFilter', 'planFilter', 'companyFilter', 'expiringFilter'];

    protected function rules()
    {
        return [
            'company_id' => 'required|exists:companies,id',
            'plan_id' => 'required|exists:plans,id',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'billing_cycle' => 'required|in:monthly,quarterly,annual',
            'amount_paid_mzn' => 'required|numeric|min:0',
            'amount_paid_usd' => 'required|numeric|min:0',
            'payment_currency' => 'required|in:MZN,USD',
            'notes' => 'nullable|string',
        ];
    }

    protected $messages = [
        'company_id.required' => 'A empresa é obrigatória.',
        'plan_id.required' => 'O plano é obrigatório.',
        'starts_at.required' => 'A data de início é obrigatória.',
        'ends_at.required' => 'A data de fim é obrigatória.',
        'ends_at.after' => 'A data de fim deve ser posterior à data de início.',
    ];
    public function render()
    {
        $subscriptions = Subscription::query()
            ->with(['company', 'plan'])
            ->when($this->search, function ($query) {
                $query->whereHas('company', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
                })->orWhereHas('plan', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($query) {
                if ($this->statusFilter === 'active') {
                    $query->active();
                } elseif ($this->statusFilter === 'expired') {
                    $query->expired();
                } else {
                    $query->where('status', $this->statusFilter);
                }
            })
            ->when($this->planFilter, function ($query) {
                $query->where('plan_id', $this->planFilter);
            })
            ->when($this->companyFilter, function ($query) {
                $query->where('company_id', $this->companyFilter);
            })
            ->when($this->expiringFilter, function ($query) {
                if ($this->expiringFilter === 'expiring_30') {
                    $query->expiringInDays(30);
                } elseif ($this->expiringFilter === 'expiring_7') {
                    $query->expiringInDays(7);
                }
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        $companies = Company::active()->orderBy('name')->get();
        $plans = Plan::active()->orderBy('sort_order')->orderBy('name')->get();
        return view('livewire.system.subscription-management',compact('subscriptions', 'companies', 'plans'))
            ->layout('layouts.system');
    }

    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    public function resetForm()
    {
        $this->editingId = null;
        $this->company_id = '';
        $this->plan_id = '';
        $this->starts_at = now()->format('Y-m-d');
        $this->ends_at = '';
        $this->billing_cycle = 'monthly';
        $this->amount_paid_mzn = '';
        $this->amount_paid_usd = '';
        $this->payment_currency = 'MZN';
        $this->notes = '';
    }

    public function updatedPlanId()
    {
        if ($this->plan_id) {
            $plan = Plan::find($this->plan_id);
            if ($plan) {
                $this->amount_paid_mzn = $plan->price_mzn;
                $this->amount_paid_usd = $plan->price_usd;
                $this->billing_cycle = $plan->billing_cycle;
                $this->calculateEndDate();
            }
        }
    }

    public function updatedStartsAt()
    {
        $this->calculateEndDate();
    }

    public function updatedBillingCycle()
    {
        $this->calculateEndDate();
    }

    private function calculateEndDate()
    {
        if ($this->starts_at && $this->billing_cycle) {
            $startDate = Carbon::parse($this->starts_at);
            $endDate = Subscription::calculateEndDate($startDate, $this->billing_cycle);
            $this->ends_at = $endDate->format('Y-m-d');
        }
    }

    public function save()
    {
        $this->validate();

        try {
            $data = [
                'company_id' => $this->company_id,
                'plan_id' => $this->plan_id,
                'starts_at' => $this->starts_at,
                'ends_at' => $this->ends_at,
                'billing_cycle' => $this->billing_cycle,
                'amount_paid_mzn' => $this->amount_paid_mzn,
                'amount_paid_usd' => $this->amount_paid_usd,
                'payment_currency' => $this->payment_currency,
                'notes' => $this->notes,
            ];

            if ($this->editingId) {
                $subscription = Subscription::findOrFail($this->editingId);
                $subscription->update($data);
                session()->flash('message', 'Subscrição atualizada com sucesso!');
            } else {
                $subscription = Subscription::create($data);
                $subscription->storePlanSnapshot();
                session()->flash('message', 'Subscrição criada com sucesso!');
            }

            $this->closeModal();

        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao salvar subscrição: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $subscription = Subscription::with(['company', 'plan'])->findOrFail($id);
        
        $this->editingId = $id;
        $this->company_id = $subscription->company_id;
        $this->plan_id = $subscription->plan_id;
        $this->starts_at = $subscription->starts_at->format('Y-m-d');
        $this->ends_at = $subscription->ends_at->format('Y-m-d');
        $this->billing_cycle = $subscription->billing_cycle;
        $this->amount_paid_mzn = $subscription->amount_paid_mzn;
        $this->amount_paid_usd = $subscription->amount_paid_usd;
        $this->payment_currency = $subscription->payment_currency;
        $this->notes = $subscription->notes;
        
        $this->showModal = true;
    }

    public function confirmCancel($id)
    {
        $this->cancelId = $id;
        $this->cancelReason = '';
        $this->showCancelModal = true;
    }

    public function cancelSubscription()
    {
        try {
            $subscription = Subscription::findOrFail($this->cancelId);
            $subscription->cancel($this->cancelReason);
            
            session()->flash('message', 'Subscrição cancelada com sucesso!');
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao cancelar subscrição: ' . $e->getMessage());
        }

        $this->showCancelModal = false;
        $this->cancelId = null;
        $this->cancelReason = '';
    }

    public function suspend($id)
    {
        try {
            $subscription = Subscription::findOrFail($id);
            $subscription->suspend();
            
            session()->flash('message', 'Subscrição suspensa com sucesso!');
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao suspender subscrição: ' . $e->getMessage());
        }
    }

    public function reactivate($id)
    {
        try {
            $subscription = Subscription::findOrFail($id);
            $subscription->reactivate();
            
            session()->flash('message', 'Subscrição reativada com sucesso!');
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao reativar subscrição: ' . $e->getMessage());
        }
    }

    public function extend($id, $days = 30)
    {
        try {
            $subscription = Subscription::findOrFail($id);
            $subscription->extend($days);
            
            session()->flash('message', "Subscrição estendida por {$days} dias!");
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao estender subscrição: ' . $e->getMessage());
        }
    }

    public function renew($id)
    {
        try {
            $subscription = Subscription::findOrFail($id);
            $subscription->renew();
            
            session()->flash('message', 'Subscrição renovada com sucesso!');
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao renovar subscrição: ' . $e->getMessage());
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingPlanFilter()
    {
        $this->resetPage();
    }

    public function updatingCompanyFilter()
    {
        $this->resetPage();
    }

    public function updatingExpiringFilter()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }
}
