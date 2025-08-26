<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'public_id',
        'role',
        'is_active',
        'phone',
        'company_id',
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

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
     * Roles associadas ao usuário
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id');
    }


    /**
     * Um usuário pode ter vários turnos (shifts)
     */
    public function shifts()
    {
        return $this->hasMany(Shift::class, 'user_id');
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


    public function emails()
    {
        return $this->morphMany(Email::class, 'emailable');
    }


    public function telephones()
    {
        return $this->morphMany(Telephone::class, 'telephonable');
    }


    public function telephonable()
    {
        return $this->morphTo();
    }

    public function addresses()
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    public function primaryTelephone()
    {
        return $this->telephones()->where('is_primary', true)->first()
            ?? $this->telephones()->first();
    }


     public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function cashMovements(): HasMany
    {
        return $this->hasMany(CashMovement::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function createdReservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'created_by');
    }

    public function approvedCashMovements(): HasMany
    {
        return $this->hasMany(CashMovement::class, 'approved_by');
    }

    // Scopes
  

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    // Methods
    public function hasActiveShift(): bool
    {
        return $this->shifts()->where('status', 'open')->exists();
    }

    public function getActiveShift(): ?Shift
    {
        return $this->shifts()->where('status', 'open')->first();
    }

    public function canManage(): bool
    {
        return in_array($this->role, ['owner', 'manager']);
    }

    public function canOperatePOS(): bool
    {
        return in_array($this->role, ['owner', 'manager', 'cashier']);
    }

    public function canManageStock(): bool
    {
        return in_array($this->role, ['owner', 'manager', 'stock_manager']);
    }

    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function isCashier(): bool
    {
        return $this->role === 'cashier';
    }
}
