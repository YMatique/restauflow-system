<?php

namespace App\Models;

use App\Models\System\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Table extends Model
{
     use HasFactory;

    protected $fillable = [
        'number',
        'name',
        'capacity',
        'status',
        'location',
        'shape',
        'position',
        'notes',
        'is_active',
        'service_charge',
        'company_id'
    ];

    protected $casts = [
        'capacity' => 'integer',
        'position' => 'array',
        'is_active' => 'boolean',
        'service_charge' => 'decimal:2'
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

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeOccupied($query)
    {
        return $query->where('status', 'occupied');
    }

    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    // Methods
    public function isAvailable(): bool
    {
        return $this->status === 'available' && $this->is_active;
    }

    public function occupy(): void
    {
        $this->update(['status' => 'occupied']);
    }

    public function free(): void
    {
        $this->update(['status' => 'available']);
    }

    public function reserve(): void
    {
        $this->update(['status' => 'reserved']);
    }

    public function setMaintenance(): void
    {
        $this->update(['status' => 'maintenance']);
    }

    public function getCurrentSale(): ?Sale
    {
        return $this->sales()
            ->where('status', 'pending')
            ->latest()
            ->first();
    }

    public function getTodaySalesCount(): int
    {
        return $this->sales()
            ->whereDate('sold_at', today())
            ->where('status', 'completed')
            ->count();
    }

    public function getTodayRevenue(): float
    {
        return $this->sales()
            ->whereDate('sold_at', today())
            ->where('status', 'completed')
            ->sum('total');
    }
}
