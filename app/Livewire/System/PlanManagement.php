<?php

namespace App\Livewire\System;

use App\Models\System\Plan;
use Livewire\Component;
use Livewire\WithPagination;

class PlanManagement extends Component
{
    use WithPagination;

    public $showModal = false;
    public $editingId = null;
    public $deleteId = null;
    public $showDeleteModal = false;

    // Form fields
    public $name = '';
    public $description = '';
    public $max_users = '';
    public $max_orders = '';
    public $features = [];
    public $price_mzn = '';
    public $price_usd = '';
    public $billing_cycle = 'monthly';
    public $is_active = true;
    public $sort_order = 0;

    // Search and filters
    public $search = '';
    public $statusFilter = '';
    public $billingCycleFilter = '';
    public $perPage = 10;

    protected $queryString = ['search', 'statusFilter', 'billingCycleFilter'];

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'max_users' => 'nullable|integer|min:1',
            'max_orders' => 'nullable|integer|min:1',
            'features' => 'nullable|array',
            'price_mzn' => 'required|numeric|min:0',
            'price_usd' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,quarterly,annual',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ];
    }

    protected $messages = [
        'name.required' => 'O nome do plano é obrigatório.',
        'price_mzn.required' => 'O preço em MZN é obrigatório.',
        'price_usd.required' => 'O preço em USD é obrigatório.',
        'billing_cycle.required' => 'O ciclo de faturação é obrigatório.',
    ];

    public function render()
    {
        
          $plans = Plan::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($query) {
                if ($this->statusFilter === 'active') {
                    $query->where('is_active', true);
                } else {
                    $query->where('is_active', false);
                }
            })
            ->when($this->billingCycleFilter, function ($query) {
                $query->where('billing_cycle', $this->billingCycleFilter);
            })
            ->ordered()
            ->paginate($this->perPage);

        $availableFeatures = Plan::getCommonFeatures();
        return view('livewire.system.plan-management',compact('plans', 'availableFeatures'))->layout('layouts.system');
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
        $this->name = '';
        $this->description = '';
        $this->max_users = '';
        $this->max_orders = '';
        $this->features = [];
        $this->price_mzn = '';
        $this->price_usd = '';
        $this->billing_cycle = 'monthly';
        $this->is_active = true;
        $this->sort_order = 0;
    }

    public function save()
    {
        $this->validate();

        try {
            $data = [
                'name' => $this->name,
                'description' => $this->description,
                'max_users' => $this->max_users ?: null,
                'max_orders' => $this->max_orders ?: null,
                'features' => $this->features,
                'price_mzn' => $this->price_mzn,
                'price_usd' => $this->price_usd,
                'billing_cycle' => $this->billing_cycle,
                'is_active' => $this->is_active,
                'sort_order' => $this->sort_order,
            ];

            if ($this->editingId) {
                $plan = Plan::findOrFail($this->editingId);
                $plan->update($data);
                session()->flash('message', 'Plano atualizado com sucesso!');
            } else {
                Plan::create($data);
                session()->flash('message', 'Plano criado com sucesso!');
            }

            $this->closeModal();

        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao salvar plano: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $plan = Plan::findOrFail($id);
        
        $this->editingId = $id;
        $this->name = $plan->name;
        $this->description = $plan->description;
        $this->max_users = $plan->max_users;
        $this->max_orders = $plan->max_orders;
        $this->features = $plan->features ?? [];
        $this->price_mzn = $plan->price_mzn;
        $this->price_usd = $plan->price_usd;
        $this->billing_cycle = $plan->billing_cycle;
        $this->is_active = $plan->is_active;
        $this->sort_order = $plan->sort_order;
        
        $this->showModal = true;
    }

    public function confirmDelete($id)
    {
        $plan = Plan::findOrFail($id);
        
        if (!$plan->canBeDeleted()) {
            session()->flash('error', 'Não é possível eliminar um plano com subscrições ativas.');
            return;
        }

        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        try {
            $plan = Plan::findOrFail($this->deleteId);
            
            if (!$plan->canBeDeleted()) {
                session()->flash('error', 'Não é possível eliminar um plano com subscrições ativas.');
                $this->showDeleteModal = false;
                return;
            }

            $plan->delete();
            session()->flash('message', 'Plano eliminado com sucesso!');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao eliminar plano: ' . $e->getMessage());
        }

        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    public function toggleStatus($id)
    {
        try {
            $plan = Plan::findOrFail($id);
            $newStatus = !$plan->is_active;
            
            $plan->update(['is_active' => $newStatus]);
            
            $statusText = $newStatus ? 'ativado' : 'desativado';
            session()->flash('message', "Plano {$statusText} com sucesso!");
            
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao alterar status: ' . $e->getMessage());
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

    public function updatingBillingCycleFilter()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }
}
