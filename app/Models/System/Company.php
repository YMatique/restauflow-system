<?php

namespace App\Models\System;

use App\Models\Category;
use App\Models\Client;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Shift;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;


class Company extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'slug',
        'address',
        'phone',
        'tax_number',
        'settings',
        'logo',
        'currency',
        'tax_rate',
        'status',
        'nuit',
        'email_verified_at',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'status' => 'string',
        'settings' => 'array',
        'tax_rate' => 'decimal:2'
    ];

    protected $attributes = [
        'status' => 'active',
    ];


    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        // Gerar um UUID para public_id antes de criar o registro
        static::creating(function ($company) {
            if (empty($company->public_id)) {
                $company->public_id = Str::uuid();
            }
        });
    }

    public function users()
    {
        return $this->hasMany(\App\Models\User::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)
            ->where('status', 'active')
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->latest('created_at');
    }
    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspended');
    }

    // Methods
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription !== null;
    }

     // Relationships
   

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function shifts(): HasMany
    {
        return $this->hasMany(Shift::class);
    }

    public function tables(): HasMany
    {
        return $this->hasMany(Table::class);
    }

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }

    public function suppliers(): HasMany
    {
        return $this->hasMany(Supplier::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function cashMovements(): HasMany
    {
        return $this->hasMany(CashMovement::class);
    }


    // Methods
    public function getFormattedTaxRateAttribute(): string
    {
        return $this->tax_rate . '%';
    }

    public function generateInvoiceNumber(): string
    {
        $lastSale = $this->sales()->orderBy('id', 'desc')->first();
        $nextNumber = $lastSale ? intval(substr($lastSale->invoice_number, -6)) + 1 : 1;
        return strtoupper($this->slug) . '-' . date('Y') . '-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }
}
