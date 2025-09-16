<?php

namespace App\Models;

use App\Models\System\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'type',
        'quantity',
        'previous_stock',
        'new_stock',
        'unit_cost',
        'reference_type',
        'reference_id',
        'reason',
        'notes',
        'date',
        'batch_number',
        'expiry_date',
        'supplier',
        'invoice_number',
        'user_id',
        'company_id'
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'previous_stock' => 'decimal:3',
        'new_stock' => 'decimal:3',
        'unit_cost' => 'decimal:4',
        'date' => 'datetime',
        'expiry_date' => 'date'
    ];

    //TYPE OF MOVIMENT
    const IN = 'in';
    const OUT = 'out';
    const SALE = 'sale'; 
    const LOSS = 'loss';
    const RETURN = 'return';
    const EXPIRED = 'expired';
    const ADJUSTMENT = 'adjustment';


    public static function typesOptions(): array
{
    return [
        self::IN            => __('messages.inventory_management.types.in'),
        self::OUT           => __('messages.inventory_management.types.out'),
        self::SALE          => __('messages.inventory_management.types.sale'),
        self::LOSS          => __('messages.inventory_management.types.loss'),
        self::RETURN        => __('messages.inventory_management.types.return'),
        self::EXPIRED       => __('messages.inventory_management.types.expired'),
        self::ADJUSTMENT    => __('messages.inventory_management.types.adjustment'),
    ];
}


    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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

    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
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

    public function getTotalValue(): float
    {
        return $this->quantity * ($this->unit_cost ?? 0);
    }
}
