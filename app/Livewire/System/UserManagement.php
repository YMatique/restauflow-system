<?php

namespace App\Livewire\System;

use App\Models\System\Company;
use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class UserManagement extends Component
{

     use WithPagination;

    // Modal states
    public $showModal = false;
    public $showDeleteModal = false;
    public $editingId = null;

    // Form properties
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $company_id = '';
    public $user_type = 'company_user';
    public $phone = '';
    public $permissions = [];
    public $is_active = true;
    public $send_welcome_email = true;

    // Filter properties
    public $search = '';
    public $companyFilter = '';
    public $userTypeFilter = '';
    public $statusFilter = '';
    public $perPage = 10;

    // Data collections
    public $companies = [];

    // Available permissions for company users
    // public $availablePermissions = [
    //     'repair_orders.create' => 'Criar Ordens de Reparação',
    //     'repair_orders.edit' => 'Editar Ordens de Reparação',
    //     'repair_orders.delete' => 'Eliminar Ordens de Reparação',
    //     'repair_orders.view' => 'Ver Ordens de Reparação',
    //     'repair_orders.export' => 'Exportar Ordens de Reparação',
    //     'employees.manage' => 'Gerir Funcionários',
    //     'clients.manage' => 'Gerir Clientes',
    //     'materials.manage' => 'Gerir Materiais',
    //     'departments.manage' => 'Gerir Departamentos',
    //     'billing.view' => 'Ver Faturação',
    //     'billing.manage' => 'Gerir Faturação',
    //     'performance.view' => 'Ver Avaliações de Desempenho',
    //     'performance.manage' => 'Gerir Avaliações de Desempenho',
    //     'reports.view' => 'Ver Relatórios',
    //     'reports.export' => 'Exportar Relatórios',
    //     'settings.manage' => 'Gerir Configurações',
    // ];

    protected $rules = [
        'name' => 'required|string|min:2|max:255',
        'email' => 'required|email|max:255',
        'password' => 'required|string|min:6|confirmed',
        'company_id' => 'required_unless:user_type,super_admin|exists:companies,id',
        'user_type' => 'required|in:super_admin,company_admin,company_user',
        'phone' => 'nullable|string|max:20',
        // 'permissions' => 'array',
        // 'permissions.*' => 'string',
        'is_active' => 'boolean',
        'send_welcome_email' => 'boolean',
    ];

    protected $messages = [
        'name.required' => 'O nome é obrigatório.',
        'name.min' => 'O nome deve ter pelo menos 2 caracteres.',
        'email.required' => 'O email é obrigatório.',
        'email.email' => 'Por favor, insira um email válido.',
        'email.unique' => 'Este email já está em uso.',
        'password.required' => 'A senha é obrigatória.',
        'password.min' => 'A senha deve ter pelo menos 6 caracteres.',
        'password.confirmed' => 'A confirmação da senha não confere.',
        'company_id.required_unless' => 'Selecione uma empresa (exceto para Super Admin).',
        'company_id.exists' => 'A empresa selecionada não existe.',
        'user_type.required' => 'Selecione um tipo de usuário.',
        'user_type.in' => 'O tipo de usuário selecionado é inválido.',
        'phone.max' => 'O telefone deve ter no máximo 20 caracteres.',
    ];

    public function mount()
    {
        $this->loadData();
    }

    public function render()
    {
          $users = $this->getUsers();
        return view('livewire.system.user-management',compact('users'))
            ->title('Gestão de Usuários')->layout('layouts.system');
    }

     public function loadData()
    {
        // Load ALL companies (Super Admin pode ver todas)
        $this->companies = Company::orderBy('name')->get();
    }

    public function getUsers()
    {
        $query = User::with('company')
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                          ->orWhere('email', 'like', '%' . $this->search . '%')
                          ->orWhere('phone', 'like', '%' . $this->search . '%')
                          ->orWhereHas('company', function ($companyQuery) {
                              $companyQuery->where('name', 'like', '%' . $this->search . '%');
                          });
                });
            })
            ->when($this->companyFilter, function ($q) {
                $q->where('company_id', $this->companyFilter);
            })
            ->when($this->userTypeFilter, function ($q) {
                $q->where('user_type', $this->userTypeFilter);
            })
            ->when($this->statusFilter, function ($q) {
                $q->where('status', $this->statusFilter);
            })
            ->orderBy('created_at', 'desc');

        return $query->paginate($this->perPage);
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
        $this->password = '';
        $this->password_confirmation = '';
        $this->company_id = '';
        $this->user_type = 'company_user';
        $this->phone = '';
        // $this->permissions = [];
        $this->is_active = true;
        $this->send_welcome_email = true;
    }

    public function edit($userId)
    {
        $user = User::findOrFail($userId);
        
        // dd($user);
        $this->editingId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->company_id = $user->company_id;
        $this->user_type = $user->user_type;
        $this->phone = $user->phone;
        // $this->permissions = $user->permissions ?? [];
        $this->is_active = $user->status === 'active';
        $this->send_welcome_email = false; // Default false for editing
        
        $this->showModal = true;
    }

    public function save()
    {
        // Dynamic validation rules based on editing or creating
        $rules = $this->rules;
        
        if ($this->editingId) {
            // For editing, password is optional
            $rules['password'] = 'nullable|string|min:6|confirmed';
            // Email unique except current user
            $rules['email'] = [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->editingId),
            ];
        } else {
            // For creating, validate unique email
            $rules['email'] = 'required|email|max:255|unique:users,email';
        }

        $this->validate($rules);

        // Check if company has reached user limit (only for non-super-admin users)
        if (!$this->editingId && $this->user_type !== 'super_admin' && !$this->canAddUser()) {
            $this->addError('company_id', 'A empresa selecionada atingiu o limite de usuários do seu plano.');
            return;
        }

        try {
            if ($this->editingId) {
                $this->updateUser();
            } else {
                $this->createUser();
            }

            $this->closeModal();
            
            $action = $this->editingId ? 'atualizado' : 'criado';
            session()->flash('success', "Usuário {$action} com sucesso!");
            
        } catch (\Exception $e) {
            $this->addError('general', 'Erro ao salvar usuário: ' . $e->getMessage());
        }
    }

    private function canAddUser()
    {
        if (!$this->company_id) {
            return false;
        }

        $company = Company::find($this->company_id);
        if (!$company) {
            return false;
        }

        // Super Admin pode adicionar usuários mesmo sem subscrição ativa
        // Mas ainda respeitamos os limites do plano se houver subscrição
        $activeSubscription = $company->activeSubscription;
        
        if (!$activeSubscription) {
            // Sem subscrição ativa, permite adicionar usuários (Super Admin preparando empresa)
            return true;
        }

        $plan = $activeSubscription->plan;
        if (!$plan->max_users) { // Unlimited users
            return true;
        }

        $currentUserCount = $company->users()->count();
        return $currentUserCount < $plan->max_users;
    }

    private function createUser()
    {
        // Set default permissions based on user type
        // $permissions = $this->permissions;
        // if ($this->user_type === 'company_admin') {
        //     $permissions = array_keys($this->availablePermissions); // All permissions
        // }

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => bcrypt($this->password),
            'company_id' => $this->user_type === 'super_admin' ? null : $this->company_id,
            'user_type' => $this->user_type,
            'phone' => $this->phone,
            // 'permissions' => $permissions,
            'status' => $this->is_active ? 'active' : 'inactive',
            'email_verified_at' => now(), // Auto-verify for admin created users
        ]);

        if ($this->send_welcome_email) {
            $this->sendWelcomeEmail($user);
        }
    }

    private function updateUser()
    {
        $user = User::findOrFail($this->editingId);
        
        // dd($this->user_type, $user);
        $updateData = [
            'name' => $this->name,
            'email' => $this->email,
            'company_id' => $this->user_type === 'super_admin' ? null : $this->company_id,
            'user_type' => $this->user_type,
            'phone' => $this->phone,
            // 'permissions' => $this->permissions,
            'status' => $this->is_active ? 'active' : 'inactive',
        ];

        // Only update password if provided
        if ($this->password) {
            $updateData['password'] = bcrypt($this->password);
        }

        $user->update($updateData);

        if ($this->send_welcome_email && $this->password) {
            $this->sendWelcomeEmail($user, true); // true = resending
        }
    }

    private function sendWelcomeEmail($user, $resending = false)
    {
        try {
            // TODO: Implementar NotificationService
            // $notificationService = app(NotificationService::class);
            // $notificationService->sendWelcomeEmail($user, $this->password, $resending);
            
            // Por enquanto apenas log
            \Log::info("Welcome email should be sent to: {$user->email}");
        } catch (\Exception $e) {
            // Log error but don't fail the user creation
            logger()->error('Failed to send welcome email: ' . $e->getMessage());
        }
    }

    public function toggleStatus($userId)
    {
        $user = User::findOrFail($userId);
        
        // Prevent self-deactivation
        if ($user->id === auth()->id()) {
            session()->flash('error', 'Você não pode alterar o seu próprio status.');
            return;
        }

        $newStatus = $user->status === 'active' ? 'inactive' : 'active';
        $user->update(['status' => $newStatus]);

        $action = $newStatus === 'active' ? 'ativado' : 'desativado';
        session()->flash('success', "Usuário {$action} com sucesso!");
    }

    public function resetPassword($userId)
    {
        $user = User::findOrFail($userId);
        
        // Generate a new random password
        $newPassword = $this->generateRandomPassword();
        
        $user->update([
            'password' => bcrypt($newPassword),
        ]);

        // Send new password via email
        try {
            // TODO: Implementar NotificationService
            \Log::info("Password reset email should be sent to: {$user->email} with password: {$newPassword}");
            
            session()->flash('success', 'Nova senha gerada: ' . $newPassword);
        } catch (\Exception $e) {
            session()->flash('error', 'Senha redefinida, mas falhou ao enviar email.');
        }
    }

    private function generateRandomPassword($length = 8)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        return substr(str_shuffle($characters), 0, $length);
    }

    public function confirmDelete($userId)
    {
        $this->editingId = $userId;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        $user = User::findOrFail($this->editingId);
        
        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            session()->flash('error', 'Você não pode eliminar a sua própria conta.');
            $this->showDeleteModal = false;
            return;
        }

        $user->delete();
        
        session()->flash('success', 'Usuário eliminado com sucesso!');
        $this->showDeleteModal = false;
        $this->editingId = null;
    }

    // Auto-update permissions when user type changes
    public function updatedUserType()
    {
        // if ($this->user_type === 'company_admin') {
        //     $this->permissions = array_keys($this->availablePermissions);
        // } elseif ($this->user_type === 'super_admin') {
        //     $this->permissions = []; // Super admin doesn't need specific permissions
        //     $this->company_id = ''; // Clear company for super admin
        // } else {
        //     // company_user gets basic permissions
        //     $this->permissions = [
        //         'repair_orders.create',
        //         'repair_orders.edit',
        //         'repair_orders.view',
        //         'reports.view',
        //     ];
        // }
    }

    // Livewire lifecycle hooks
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCompanyFilter()
    {
        $this->resetPage();
    }

    public function updatingUserTypeFilter()
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
