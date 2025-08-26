<?php

namespace App\Models;

use App\Models\System\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_code',
        'client_id',
        'table_id',
        'reserved_at',
        'arrived_at',
        'party_size',
        'status',
        'special_requests',
        'occasion',
        'notes',
        'phone',
        'deposit',
        'created_by',
        'company_id'
    ];

    protected $casts = [
        'reserved_at' => 'datetime',
        'arrived_at' => 'datetime',
        'party_size' => 'integer',
        'deposit' => 'decimal:2'
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('reserved_at', today());
    }

    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('reserved_at', '>', now())
                    ->whereIn('status', ['confirmed', 'arrived']);
    }

    // Methods
    public function markArrived(): void
    {
        $this->update([
            'status' => 'arrived',
            'arrived_at' => now()
        ]);
    }

    public function assignTable(Table $table): void
    {
        $this->update([
            'table_id' => $table->id,
            'status' => 'seated'
        ]);

        $table->occupy();
    }

    public function complete(): void
    {
        $this->update(['status' => 'completed']);

        // Free the table if assigned
        if ($this->table) {
            $this->table->free();
        }
    }

    public function cancel(): void
    {
        $this->update(['status' => 'cancelled']);

        // Free the table if was reserved
        if ($this->table && $this->table->status === 'reserved') {
            $this->table->free();
        }
    }

    public function noShow(): void
    {
        $this->update(['status' => 'no_show']);

        // Free the table
        if ($this->table && $this->table->status === 'reserved') {
            $this->table->free();
        }
    }

    public function isToday(): bool
    {
        return $this->reserved_at->isToday();
    }

    public function isPast(): bool
    {
        return $this->reserved_at->isPast();
    }

    public function getTimeUntilReservation(): string
    {
        return $this->reserved_at->diffForHumans();
    }

    // Boot method for auto-generation
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($reservation) {
            if (!$reservation->reservation_code) {
                $reservation->reservation_code = 'RES-' . strtoupper(Str::random(8));
            }
        });
    }
}
