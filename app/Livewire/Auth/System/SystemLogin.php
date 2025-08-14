<?php

namespace App\Livewire\Auth\System;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Str;
use Livewire\Component;

class SystemLogin extends Component
{
     public string $email = '';
    public string $password = '';
    public bool $remember = false;

    protected $rules = [
        'email' => ['required', 'string', 'email'],
        'password' => ['required', 'string'],
    ];

    protected $messages = [
        'email.required' => 'O email é obrigatório.',
        'email.email' => 'Digite um email válido.',
        'password.required' => 'A senha é obrigatória.',
    ];

    #[Layout('layouts.auth-system')]
    #[Title('Login do Sistema')]

     public function login()
    {
        $this->validate();


        $this->ensureIsNotRateLimited();

        if (!Auth::attempt($this->only(['email', 'password']), $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            // Log failed attempt
            // $logger = app(ActivityLoggerService::class);
            // $logger->logFailedLogin($this->email);

            throw ValidationException::withMessages([
                'email' => 'Credenciais inválidas ou acesso não autorizado.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());

        session()->regenerate();

        $user = Auth::user();

        // Verificar se o usuário está ativo
        if ($user->status !== 'active') {
            Auth::logout();
            session()->flash('error', 'Sua conta está inativa. Contacte o administrador.');
            return;
        }

        // Verificar se é Super Admin
        if (!$user->isSuperAdmin()) {
            Auth::logout();
            
            // Log unauthorized access attempt
            

            throw ValidationException::withMessages([
                'email' => 'Acesso negado. Apenas Super Administradores podem acessar esta área.',
            ]);
        }

        // Password reset required?
        if ($user->password_reset_required) {
            return $this->redirect(route('password.reset.required'), navigate: true);
        }

        
        
        session()->flash('message', "Bem-vindo ao Sistema, {$user->name}!");
        
        return $this->redirect(route('system.dashboard'), navigate: true);
    }

    protected function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email) . '|' . request()->ip());
    }
    public function render()
    {
        return view('livewire.auth.system.system-login');
    }
}
