<?php

namespace App\Models;

use App\Models\System\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Shift extends Model
{

     use HasFactory;

    protected $fillable = [
        'user_id',
        'opened_at',
        'initial_amount',
        'closed_at',
        'final_amount',
        'expected_amount',
        'difference',
        'withdrawals',
        'status',
        'opening_notes',
        'closing_notes',
        'total_orders',
        'total_sales',
        'cash_sales',
        'card_sales',
        'digital_sales',
        'terminal_id',
        'company_id'
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'initial_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'expected_amount' => 'decimal:2',
        'difference' => 'decimal:2',
        'withdrawals' => 'decimal:2',
        'total_orders' => 'integer',
        'total_sales' => 'decimal:2',
        'cash_sales' => 'decimal:2',
        'card_sales' => 'decimal:2',
        'digital_sales' => 'decimal:2'
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function cashMovements(): HasMany
    {
        return $this->hasMany(CashMovement::class);
    }

    // Scopes
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('opened_at', today());
    }

    // Methods
    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    public function getDuration(): ?int
    {
        if (!$this->closed_at) {
            return $this->opened_at->diffInMinutes(now());
        }

        return $this->opened_at->diffInMinutes($this->closed_at);
    }

    // public function getDurationFormatted(): string
    // {
    //     $minutes = $this->getDuration();
    //     if (!$minutes) return '0min';

    //     $hours = intval($minutes / 60);
    //     $mins = $minutes % 60;

    //     return $hours > 0 ? "{$hours}h {$mins}min" : "{$mins}min";
    // }

    public function calculateExpectedAmount(): float
    {
        return $this->initial_amount + $this->cash_sales - $this->withdrawals;
    }

    // public function close(float $finalAmount, ?string $notes = null, ?float $withdrawals = null): void
    // {
    //     // Update withdrawals if provided
    //     if ($withdrawals !== null) {
    //         $this->withdrawals = $withdrawals;
    //     }

    //     $expectedAmount = $this->calculateExpectedAmount();
        
    //     $this->update([
    //         'closed_at' => now(),
    //         'final_amount' => $finalAmount,
    //         'expected_amount' => $expectedAmount,
    //         'difference' => $finalAmount - $expectedAmount,
    //         'closing_notes' => $notes,
    //         'status' => 'closed'
    //     ]);
    // }

    public function updateSalesMetrics(): void
    {
        $sales = $this->sales()->where('status', 'completed');
        
        $this->update([
            'total_orders' => $sales->count(),
            'total_sales' => $sales->sum('total'),
            'cash_sales' => $sales->where('payment_method', 'cash')->sum('total'),
            'card_sales' => $sales->where('payment_method', 'card')->sum('total'),
            'digital_sales' => $sales->whereIn('payment_method', ['mpesa', 'mbway'])->sum('total')
        ]);
    }

    public function getAverageOrderValue(): float
    {
        return $this->total_orders > 0 ? $this->total_sales / $this->total_orders : 0;
    }

    // public function addWithdrawal(float $amount, string $description, ?int $userId = null): void
    // {
    //     $this->increment('withdrawals', $amount);

    //     // Create cash movement record
    //     CashMovement::create([
    //         'shift_id' => $this->id,
    //         'type' => 'out',
    //         'amount' => $amount,
    //         'description' => $description,
    //         'category' => 'withdrawal',
    //         'date' => now(),
    //         'user_id' => $userId ?? auth()->id(),
    //         'company_id' => $this->company_id
    //     ]);
    // }


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
}
