<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;



/**
 * @property int $company_id
 */
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




    /**
 * Get the active shift for this user
 */
public function getActiveShift(): ?Shift
{
    return $this->shifts()
                ->where('status', 'open')
                ->latest()
                ->first();
}

/**
 * Check if user has an active shift
 */
public function hasActiveShift(): bool
{
    return $this->getActiveShift() !== null;
}

/**
 * Check if user can access POS
 */
public function canUsePOS(): bool
{
    return $this->is_active &&
           in_array($this->role, ['owner', 'manager', 'cashier']) &&
           $this->hasActiveShift();
}



/**
 * Check if user can perform action based on role
 */
public function canPerformAction(string $action): bool
{
    $permissions = [
        'owner' => ['*'], // All permissions
        'manager' => ['pos', 'reports', 'stock', 'products', 'shifts'],
        'cashier' => ['pos'],
        'stock_manager' => ['stock', 'products'],
        'waiter' => ['pos_limited'] // Limited POS access
    ];

    $userPermissions = $permissions[$this->role] ?? [];

    return in_array('*', $userPermissions) || in_array($action, $userPermissions);
}

// ===================================================================
// Adicionar ao app/Models/Shift.php
// ===================================================================

/**
 * Get formatted duration of shift
 */
public function getDurationFormatted(): string
{
    if (!$this->opened_at) {
        return '0h 0min';
    }

    $endTime = $this->closed_at ?? now();
    $duration = $this->opened_at->diffInMinutes($endTime);

    $hours = floor($duration / 60);
    $minutes = $duration % 60;

    return "{$hours}h {$minutes}min";
}

/**
 * Get shift duration in minutes
 */
public function getDurationInMinutes(): int
{
    if (!$this->opened_at) {
        return 0;
    }

    $endTime = $this->closed_at ?? now();
    return $this->opened_at->diffInMinutes($endTime);
}

/**
 * Close the shift
 */
public function close(float $finalAmount, ?string $notes = null, float $withdrawals = 0): void
{
    $this->update([
        'closed_at' => now(),
        'final_amount' => $finalAmount,
        'closing_notes' => $notes,
        'withdrawals' => $withdrawals,
        'status' => 'closed'
    ]);

    // Calculate difference
    $expectedAmount = $this->initial_amount + $this->total_sales - $withdrawals;
    $difference = $finalAmount - $expectedAmount;

    $this->update(['cash_difference' => $difference]);

    // Create closing cash movement
    $this->cashMovements()->create([
        'type' => 'closing',
        'amount' => $finalAmount,
        'description' => "Fechamento do turno #{$this->id}",
        'category' => 'closing',
        'date' => now(),
        'user_id' => $this->user_id,
        'company_id' => $this->company_id
    ]);
}

/**
 * Get shift statistics
 */
public function getStats(): array
{
    return [
        'duration' => $this->getDurationFormatted(),
        'duration_minutes' => $this->getDurationInMinutes(),
        'total_sales' => $this->total_sales ?? 0,
        'total_orders' => $this->total_orders ?? 0,
        'average_order' => $this->total_orders > 0 ? $this->total_sales / $this->total_orders : 0,
        'cash_in' => $this->initial_amount,
        'cash_out' => $this->withdrawals ?? 0,
        'expected_cash' => ($this->initial_amount + $this->total_sales) - ($this->withdrawals ?? 0),
        'actual_cash' => $this->final_amount ?? 0,
        'difference' => $this->cash_difference ?? 0,
        'sales_by_payment_method' => $this->getSalesByPaymentMethod(),
        'hourly_sales' => $this->getHourlySales()
    ];
}

/**
 * Get sales breakdown by payment method
 */
public function getSalesByPaymentMethod(): array
{
    return $this->sales()
                ->selectRaw('payment_method, SUM(total) as total_amount, COUNT(*) as count')
                ->where('status', 'completed')
                ->groupBy('payment_method')
                ->pluck('total_amount', 'payment_method')
                ->toArray();
}

/**
 * Get hourly sales data
 */
public function getHourlySales(): array
{
    $sales = $this->sales()
                  ->where('status', 'completed')
                  ->selectRaw('HOUR(sold_at) as hour, SUM(total) as total_amount, COUNT(*) as count')
                  ->groupBy('hour')
                  ->orderBy('hour')
                  ->get();

    $hourlySales = [];
    for ($i = 0; $i < 24; $i++) {
        $hourlySales[$i] = [
            'hour' => sprintf('%02d:00', $i),
            'total_amount' => 0,
            'count' => 0
        ];
    }

    foreach ($sales as $sale) {
        $hourlySales[$sale->hour] = [
            'hour' => sprintf('%02d:00', $sale->hour),
            'total_amount' => (float) $sale->total_amount,
            'count' => (int) $sale->count
        ];
    }

    return array_values($hourlySales);
}

/**
 * Check if shift can be closed
 */
public function canBeClosed(): bool
{
    return $this->status === 'open' && $this->opened_at !== null;
}

/**
 * Get shift performance rating
 */
public function getPerformanceRating(): string
{
    $averageOrderValue = $this->total_orders > 0 ? $this->total_sales / $this->total_orders : 0;
    $salesPerHour = $this->getDurationInMinutes() > 0 ? ($this->total_sales / $this->getDurationInMinutes()) * 60 : 0;

    if ($salesPerHour > 5000 && $averageOrderValue > 300) {
        return 'excellent';
    } elseif ($salesPerHour > 3000 && $averageOrderValue > 200) {
        return 'good';
    } elseif ($salesPerHour > 1500 && $averageOrderValue > 150) {
        return 'average';
    } else {
        return 'needs_improvement';
    }
}

/**
 * Add withdrawal to shift
 */
public function addWithdrawal(float $amount, string $description, ?int $userId = null): void
{
    $this->increment('withdrawals', $amount);

    // Create cash movement record
    $this->cashMovements()->create([
        'type' => 'out',
        'amount' => $amount,
        'description' => $description,
        'category' => 'withdrawal',
        'date' => now(),
        'user_id' => $userId ?? auth()->id(),
        'company_id' => $this->company_id
    ]);
}

// ===================================================================
// Adicionar ao app/Models/Product.php
// ===================================================================

/**
 * Check if product can be sold
 */
public function canSell(float $quantity = 1): bool
{
    if (!$this->is_active) {
        return false;
    }

    // Check stock if tracking is enabled
    if ($this->track_stock) {
        return $this->stock_quantity >= $quantity;
    }

    return true;
}

/**
 * Get stock status
 */
public function getStockStatus(): string
{
    if (!$this->track_stock) {
        return 'in_stock';
    }

    if ($this->stock_quantity <= 0) {
        return 'out_of_stock';
    } elseif ($this->stock_quantity <= $this->min_stock_level) {
        return 'low_stock';
    } else {
        return 'in_stock';
    }
}

/**
 * Check if product needs restock alert
 */
public function needsRestockAlert(): bool
{
    return $this->track_stock && $this->stock_quantity <= $this->min_stock_level;
}

// ===================================================================
// Adicionar ao app/Models/Table.php
// ===================================================================

/**
 * Check if table is available
 */
public function isAvailable(): bool
{
    return $this->is_active && $this->status === 'available';
}

/**
 * Mark table as occupied
 */
public function markOccupied(): void
{
    $this->update(['status' => 'occupied']);
}

/**
 * Mark table as available
 */
public function markAvailable(): void
{
    $this->update(['status' => 'available']);
}

/**
 * Get table status badge
 */
public function getStatusBadge(): array
{
    $badges = [
        'available' => ['color' => 'green', 'text' => 'Disponível', 'icon' => '✅'],
        'occupied' => ['color' => 'red', 'text' => 'Ocupada', 'icon' => '🔴'],
        'reserved' => ['color' => 'yellow', 'text' => 'Reservada', 'icon' => '🟡'],
        'maintenance' => ['color' => 'gray', 'text' => 'Manutenção', 'icon' => '🔧']
    ];

    return $badges[$this->status] ?? $badges['available'];
}
}
