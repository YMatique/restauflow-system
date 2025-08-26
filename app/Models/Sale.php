<?php

namespace App\Models;

use App\Models\System\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sale extends Model
{

    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'client_id',
        'table_id',
        'shift_id',
        'user_id',
        'sold_at',
        'completed_at',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'service_charge',
        'total',
        'payment_method',
        'payment_details',
        'status',
        'notes',
        'sale_type',
        'customer_count',
        'split_details',
        'company_id'
    ];

    protected $casts = [
        'sold_at' => 'datetime',
        'completed_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'service_charge' => 'decimal:2',
        'total' => 'decimal:2',
        'payment_details' => 'array',
        'split_details' => 'array',
        'customer_count' => 'integer'
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

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function cashMovements(): HasMany
    {
        return $this->hasMany(CashMovement::class, 'reference_id')
            ->where('reference_type', Sale::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('sold_at', today());
    }

    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByPaymentMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    // Methods
    public function addItem(Product $product, float $quantity): SaleItem
    {
        return $this->saleItems()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => $quantity,
            'unit_price' => $product->price,
            'total_price' => $product->price * $quantity,
            'company_id' => $this->company_id
        ]);
    }

    public function calculateTotals(): void
    {
        $subtotal = $this->saleItems()->sum('total_price');
        $taxRate = $this->company->tax_rate / 100;
        $taxAmount = $subtotal * $taxRate;
        $total = $subtotal + $taxAmount + $this->service_charge - $this->discount_amount;

        $this->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total' => $total
        ]);
    }

    public function complete(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);

        // Update shift metrics
        $this->shift->updateSalesMetrics();

        // Update client last visit
        if ($this->client) {
            $this->client->updateLastVisit();
        }

        // Free table if dine-in
        if ($this->table && $this->sale_type === 'dine_in') {
            $this->table->free();
        }

        // Reduce stock for products
        foreach ($this->saleItems as $item) {
            $item->product->reduceStock(
                $item->quantity,
                "Sale #{$this->invoice_number}",
                $this->user_id
            );
        }

        // Create cash movement if cash payment
        if ($this->payment_method === 'cash') {
            CashMovement::create([
                'shift_id' => $this->shift_id,
                'type' => 'in',
                'amount' => $this->total,
                'description' => "Sale #{$this->invoice_number}",
                'category' => 'sale',
                'date' => $this->completed_at,
                'reference_type' => Sale::class,
                'reference_id' => $this->id,
                'user_id' => $this->user_id,
                'company_id' => $this->company_id
            ]);
        }
    }

    public function cancel(string $reason = null): void
    {
        $this->update([
            'status' => 'cancelled',
            'notes' => $reason ? "Cancelled: {$reason}" : 'Cancelled'
        ]);

        // Free table if was occupied
        if ($this->table && $this->table->status === 'occupied') {
            $this->table->free();
        }
    }

    public function getItemsCount(): int
    {
        return $this->saleItems()->sum('quantity');
    }

    public function getAverageItemPrice(): float
    {
        $totalItems = $this->getItemsCount();
        return $totalItems > 0 ? $this->subtotal / $totalItems : 0;
    }

    // Boot method for auto-generation
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($sale) {
            if (!$sale->invoice_number) {
                $sale->invoice_number = $sale->company->generateInvoiceNumber();
            }
            if (!$sale->sold_at) {
                $sale->sold_at = now();
            }
        });
    }
}
