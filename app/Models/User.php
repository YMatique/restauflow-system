<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn($word) => Str::substr($word, 0, 1))
            ->implode('');
    }
    public function isCompanyUser(): bool
    {
        return $this->user_type === 'company_user';
    }
    public function company()
    {
        return $this->belongsTo(\App\Models\System\Company::class);
    }
    /**
     * Scope para usuários ativos
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompanyAdmin($query)
    {
        return $query->where('user_type', 'company_admin');
    }
    public function scopeOfCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }
    public function scopeOnline($query)
    {
        return $query->where('last_login_at', '>=', now()->subMinutes(15));
    }

    public function getUserTypeTextAttribute(): string
    {
        return match ($this->user_type) {
            'super_admin' => 'Super Administrador',
            'company_admin' => 'Administrador da Empresa',
            'company_user' => 'Usuário',
            default => 'Usuário',
        };
    }

    /**
     * Get status text
     */
    public function getStatusTextAttribute(): string
    {
        return match ($this->status) {
            'active' => 'Ativo',
            'inactive' => 'Inativo',
            'suspended' => 'Suspenso',
            default => 'Inativo',
        };
    }

    /**
     * Check if user is online
     */
    public function getIsOnlineAttribute(): bool
    {
        if (!$this->last_login_at) {
            return false;
        }

        return $this->last_login_at->greaterThan(now()->subMinutes(15));
    }

    /**
     * Get avatar URL
     */
    public function getAvatarUrlAttribute(): ?string
    {
        // Por enquanto, retorna null. Depois pode implementar upload de avatar
        return null;
    }

    /**
     * Get user initials for avatar
     */
    public function getInitialsAttribute(): string
    {
        $words = explode(' ', $this->name);
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        return strtoupper(substr($this->name, 0, 2));
    }

    // ===== HELPER METHODS =====

    /**
     * Check if user is super admin
     */
    // public function isSuperAdmin(): bool
    // {
    //     return $this->user_type === 'super_admin' || $this->is_super_admin;
    // }

    /**
     * Check if user is company admin
     */
    public function isCompanyAdmin(): bool
    {
        return $this->user_type === 'company_admin';
    }

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

     public function isSuperAdmin(): bool
    {
        return $this->user_type === 'super_admin' || $this->is_super_admin == true;
    }
     public function scopeSuperAdmin($query)
    {
        return $query->where('user_type', 'super_admin')->orWhere('is_super_admin', true);
    }
}
