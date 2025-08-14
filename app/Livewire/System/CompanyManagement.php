<?php

namespace App\Livewire\System;

use App\Models\System\Company;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class CompanyManagement extends Component
{
      use WithPagination;

    public $showModal = false;
    public $editingId = null;
    public $deleteId = null;
    public $showDeleteModal = false;

    // Form fields
    public $name = '';
    public $email = '';
    public $address = '';
    public $phone = '';
    public $nuit = '';
    public $status = 'active';

    // Search and filters
    public $search = '';
    public $statusFilter = '';
    public $perPage = 10;

    protected $queryString = ['search', 'statusFilter'];

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('companies', 'email')->ignore($this->editingId)
            ],
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'nuit' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive,suspended',
        ];
    }

    protected $messages = [
        'name.required' => 'O nome da empresa é obrigatório.',
        'email.required' => 'O email é obrigatório.',
        'email.email' => 'Digite um email válido.',
        'email.unique' => 'Este email já está sendo usado por outra empresa.',
        'status.required' => 'O status é obrigatório.',
    ];
    public function render()
    {
             $companies = Company::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('nuit', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);
        return view('livewire.system.company-management', compact('companies'))->layout('layouts.system');
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
        $this->email = '';
        $this->address = '';
        $this->phone = '';
        $this->nuit = '';
        $this->status = 'active';
    }

    public function save()
    {
        $this->validate();

        try {
            $data = [
                'name' => $this->name,
                'email' => $this->email,
                'address' => $this->address,
                'phone' => $this->phone,
                'nuit' => $this->nuit,
                'status' => $this->status,
            ];

            if ($this->editingId) {
                $company = Company::findOrFail($this->editingId);
                $company->update($data);
                session()->flash('message', 'Empresa atualizada com sucesso!');
            } else {
                Company::create($data);
                session()->flash('message', 'Empresa criada com sucesso!');
            }

            $this->closeModal();

        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao salvar empresa: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $company = Company::findOrFail($id);
        
        $this->editingId = $id;
        $this->name = $company->name;
        $this->email = $company->email;
        $this->address = $company->address;
        $this->phone = $company->phone;
        $this->nuit = $company->nuit;
        $this->status = $company->status;
        
        $this->showModal = true;
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        try {
            $company = Company::findOrFail($this->deleteId);
            
            // Check if company has active subscriptions or users
            if ($company->users()->exists()) {
                session()->flash('error', 'Não é possível eliminar empresa com usuários associados.');
                $this->showDeleteModal = false;
                return;
            }

            $company->delete();
            session()->flash('message', 'Empresa eliminada com sucesso!');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao eliminar empresa: ' . $e->getMessage());
        }

        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    public function toggleStatus($id)
    {
        try {
            $company = Company::findOrFail($id);
            $newStatus = $company->status === 'active' ? 'inactive' : 'active';
            
            $company->update(['status' => $newStatus]);
            
            $statusText = $newStatus === 'active' ? 'ativada' : 'desativada';
            session()->flash('message', "Empresa {$statusText} com sucesso!");
            
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

    public function updatingPerPage()
    {
        $this->resetPage();
    }
}
