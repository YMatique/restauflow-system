<?php

namespace App\Models;

use App\Models\System\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $fillable = [
        'name',
        'document',
        'phone',
        'email',
        'address',
        'preferences',
        'credit_limit',
        'current_balance',
        'loyalty_points',
        'is_vip',
        'is_active',
        'last_visit',
        'company_id'
    ];

    protected $casts = [
        'preferences' => 'array',
        'credit_limit' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'loyalty_points' => 'integer',
        'is_vip' => 'boolean',
        'is_active' => 'boolean',
        'last_visit' => 'datetime'
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVip($query)
    {
        return $query->where('is_vip', true);
    }

    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    // Methods
    public function getTotalSpent(): float
    {
        return $this->sales()
            ->where('status', 'completed')
            ->sum('total');
    }

    public function getVisitCount(): int
    {
        return $this->sales()
            ->where('status', 'completed')
            ->count();
    }

    public function updateLastVisit(): void
    {
        $this->update(['last_visit' => now()]);
    }

    public function addLoyaltyPoints(int $points): void
    {
        $this->increment('loyalty_points', $points);
    }

    public function deductBalance(float $amount): void
    {
        $this->decrement('current_balance', $amount);
    }

    public function addBalance(float $amount): void
    {
        $this->increment('current_balance', $amount);
    }

    public function canPurchase(float $amount): bool
    {
        return $this->current_balance >= $amount || 
               ($this->credit_limit > 0 && ($amount - $this->current_balance) <= $this->credit_limit);
    }
}
