<?php

namespace App\Models;

use App\Models\System\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashMovement extends Model
{

    use HasFactory;

    protected $fillable = [
        'shift_id',
        'type',
        'amount',
        'description',
        'category',
        'date',
        'notes',
        'reference_type',
        'reference_id',
        'approved_by',
        'approved_at',
        'user_id',
        'company_id'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'datetime',
        'approved_at' => 'datetime'
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopeIn($query)
    {
        return $query->where('type', 'in');
    }

    public function scopeOut($query)
    {
        return $query->where('type', 'out');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('date', today());
    }

    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeApproved($query)
    {
        return $query->whereNotNull('approved_at');
    }

    public function scopePending($query)
    {
        return $query->whereNull('approved_at');
    }

    // Methods
    public function isIncoming(): bool
    {
        return $this->type === 'in';
    }

    public function isOutgoing(): bool
    {
        return $this->type === 'out';
    }

    public function isApproved(): bool
    {
        return !is_null($this->approved_at);
    }

    public function approve(?int $approvedBy = null): void
    {
        $this->update([
            'approved_by' => $approvedBy ?? auth()->id(),
            'approved_at' => now()
        ]);
    }

    public function requiresApproval(): bool
    {
        return $this->type === 'out' && in_array($this->category, ['withdrawal', 'expense']) && $this->amount > 1000;
    }
}
