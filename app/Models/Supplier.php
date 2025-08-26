<?php

namespace App\Models;

use App\Models\System\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'company_name',
        'contact_person',
        'email',
        'phone',
        'address',
        'tax_number',
        'payment_terms',
        'credit_limit',
        'current_balance',
        'payment_days',
        'notes',
        'is_active',
        'company_id'
    ];

    protected $casts = [
        'payment_terms' => 'array',
        'credit_limit' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'payment_days' => 'integer',
        'is_active' => 'boolean'
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'supplier', 'name');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    // Methods
    public function getTotalPurchases(): float
    {
        return $this->stockMovements()
            ->where('type', 'in')
            ->whereNotNull('unit_cost')
            ->get()
            ->sum(function ($movement) {
                return $movement->quantity * $movement->unit_cost;
            });
    }

    public function getLastPurchaseDate(): ?string
    {
        $lastMovement = $this->stockMovements()
            ->where('type', 'in')
            ->latest('date')
            ->first();

        return $lastMovement ? $lastMovement->date->format('Y-m-d') : null;
    }

    public function addBalance(float $amount): void
    {
        $this->increment('current_balance', $amount);
    }

    public function deductBalance(float $amount): void
    {
        $this->decrement('current_balance', $amount);
    }

    public function isOverCreditLimit(): bool
    {
        return $this->current_balance > $this->credit_limit;
    }
}
