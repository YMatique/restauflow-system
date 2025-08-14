<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
     use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'max_users',
        'max_orders',
        'features',
        'price_mzn',
        'price_usd',
        'billing_cycle',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'features' => 'array',
        'price_mzn' => 'decimal:2',
        'price_usd' => 'decimal:2',
        'is_active' => 'boolean',
        'max_users' => 'integer',
        'max_orders' => 'integer',
        'sort_order' => 'integer',
    ];

    protected $attributes = [
        'is_active' => true,
        'sort_order' => 0,
        'billing_cycle' => 'monthly',
    ];

    // Relationships
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscriptions()
    {
        return $this->hasMany(Subscription::class)->where('status', 'active');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // Accessors & Mutators
    public function getPriceMznFormattedAttribute(): string
    {
        return number_format($this->price_mzn, 2, ',', '.') . ' MZN';
    }

    public function getPriceUsdFormattedAttribute(): string
    {
        return '$' . number_format($this->price_usd, 2, '.', ',');
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

    // Methods
    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function hasUnlimitedUsers(): bool
    {
        return $this->max_users === null;
    }

    public function hasUnlimitedOrders(): bool
    {
        return $this->max_orders === null;
    }

    public function hasFeature(string $feature): bool
    {
        return in_array($feature, $this->features ?? []);
    }

    public function getSubscriptionsCount(): int
    {
        return $this->subscriptions()->count();
    }

    public function getActiveSubscriptionsCount(): int
    {
        return $this->activeSubscriptions()->count();
    }

    public function canBeDeleted(): bool
    {
        return $this->subscriptions()->count() === 0;
    }

    // Static methods for common features
    public static function getCommonFeatures(): array
    {
        return [
            'dashboard' => 'Dashboard Principal',
            'repair_orders' => 'Ordens de Reparação',
            'billing' => 'Faturação',
            'employees' => 'Gestão de Funcionários',
            'clients' => 'Gestão de Clientes',
            'materials' => 'Gestão de Materiais',
            'performance' => 'Avaliação de Desempenho',
            'reports' => 'Relatórios',
            'export' => 'Exportação de Dados',
            'notifications' => 'Notificações',
            'multi_currency' => 'Multi-moedas',
            'advanced_search' => 'Pesquisa Avançada',
            'api_access' => 'Acesso API',
            'priority_support' => 'Suporte Prioritário',
        ];
    }
}
