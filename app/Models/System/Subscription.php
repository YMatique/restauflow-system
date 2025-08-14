<?php

namespace App\Models\System;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
     use HasFactory;

    protected $fillable = [
        'company_id',
        'plan_id',
        'starts_at',
        'ends_at',
        'status',
        'amount_paid_mzn',
        'amount_paid_usd',
        'payment_currency',
        'billing_cycle',
        'plan_snapshot',
        'notes',
        'cancelled_at',
        'cancelled_reason',
    ];

    protected $casts = [
        'starts_at' => 'date',
        'ends_at' => 'date',
        'amount_paid_mzn' => 'decimal:2',
        'amount_paid_usd' => 'decimal:2',
        'plan_snapshot' => 'array',
        'cancelled_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => 'active',
        'payment_currency' => 'MZN',
        'billing_cycle' => 'monthly',
    ];

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('starts_at', '<=', now())
                    ->where('ends_at', '>=', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired')
                    ->orWhere(function($q) {
                        $q->where('status', 'active')
                          ->where('ends_at', '<', now());
                    });
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspended');
    }

    public function scopeExpiringInDays($query, $days = 30)
    {
        return $query->where('status', 'active')
                    ->whereBetween('ends_at', [now(), now()->addDays($days)]);
    }

    // Accessors & Mutators
    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'active' => 'Ativa',
            'expired' => 'Expirada',
            'cancelled' => 'Cancelada',
            'suspended' => 'Suspensa',
            default => $this->status,
        };
    }

    public function getBillingCycleTextAttribute(): string
    {
        return match($this->billing_cycle) {
            'monthly' => 'Mensal',
            'quarterly' => 'Trimestral',
            'annual' => 'Anual',
            default => $this->billing_cycle,
        };
    }

    public function getAmountPaidFormattedAttribute(): string
    {
        if ($this->payment_currency === 'USD') {
            return '$' . number_format($this->amount_paid_usd, 2, '.', ',');
        }
        return number_format($this->amount_paid_mzn, 2, ',', '.') . ' MZN';
    }

    public function getDaysRemainingAttribute(): int
    {
        if ($this->status !== 'active') {
            return 0;
        }
        
        return max(0, now()->diffInDays($this->ends_at, false));
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->ends_at < now();
    }

    public function getIsExpiringAttribute(): bool
    {
        return $this->status === 'active' && $this->days_remaining <= 30;
    }

    // Methods
    public function isActive(): bool
    {
        return $this->status === 'active' 
            && $this->starts_at <= now() 
            && $this->ends_at >= now();
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired' || $this->ends_at < now();
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    public function cancel(string $reason = null): bool
    {
        return $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancelled_reason' => $reason,
        ]);
    }

    public function suspend(): bool
    {
        return $this->update(['status' => 'suspended']);
    }

    public function reactivate(): bool
    {
        return $this->update([
            'status' => 'active',
            'cancelled_at' => null,
            'cancelled_reason' => null,
        ]);
    }

    public function extend(int $days): bool
    {
        return $this->update([
            'ends_at' => $this->ends_at->addDays($days),
        ]);
    }

    public function renew(): bool
    {
        $newStartDate = $this->ends_at->addDay();
        $newEndDate = $this->calculateEndDate($newStartDate, $this->billing_cycle);

        return $this->update([
            'starts_at' => $newStartDate,
            'ends_at' => $newEndDate,
            'status' => 'active',
        ]);
    }

    public function storePlanSnapshot(): void
    {
        if ($this->plan) {
            $this->update([
                'plan_snapshot' => [
                    'name' => $this->plan->name,
                    'description' => $this->plan->description,
                    'max_users' => $this->plan->max_users,
                    'max_orders' => $this->plan->max_orders,
                    'features' => $this->plan->features,
                    'price_mzn' => $this->plan->price_mzn,
                    'price_usd' => $this->plan->price_usd,
                ]
            ]);
        }
    }

    public function getPlanName(): string
    {
        return $this->plan_snapshot['name'] ?? $this->plan?->name ?? 'Plano Removido';
    }

    public function getPlanFeatures(): array
    {
        return $this->plan_snapshot['features'] ?? $this->plan?->features ?? [];
    }

    public function canUseFeature(string $feature): bool
    {
        return in_array($feature, $this->getPlanFeatures());
    }

    // Static methods
    public static function calculateEndDate(Carbon $startDate, string $billingCycle): Carbon
    {
        return match($billingCycle) {
            'monthly' => $startDate->copy()->addMonth()->subDay(),
            'quarterly' => $startDate->copy()->addMonths(3)->subDay(),
            'annual' => $startDate->copy()->addYear()->subDay(),
            default => $startDate->copy()->addMonth()->subDay(),
        };
    }

    public static function createForCompany(Company $company, Plan $plan, array $data = []): self
    {
        $startDate = Carbon::parse($data['starts_at'] ?? now());
        $endDate = self::calculateEndDate($startDate, $data['billing_cycle'] ?? $plan->billing_cycle);

        $subscription = self::create(array_merge([
            'company_id' => $company->id,
            'plan_id' => $plan->id,
            'starts_at' => $startDate,
            'ends_at' => $endDate,
            'billing_cycle' => $plan->billing_cycle,
            'amount_paid_mzn' => $plan->price_mzn,
            'amount_paid_usd' => $plan->price_usd,
        ], $data));

        $subscription->storePlanSnapshot();

        return $subscription;
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($subscription) {
            // Automatically expire other active subscriptions for the same company
            self::where('company_id', $subscription->company_id)
                ->where('status', 'active')
                ->update(['status' => 'expired']);
        });
    }
}
