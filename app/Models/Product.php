<?php

namespace App\Models;

use App\Models\System\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
     protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'cost',
        'type',
        'stock_quantity',
        'min_level',
        'cost_per_unit',
        'image',
        'images',
        'barcode',
        'track_stock',
        'is_available',
        'is_active',
        'is_featured',
        'company_id'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
        'stock_quantity' => 'decimal:3',
        'min_level' => 'decimal:3',
        'cost_per_unit' => 'decimal:2',
        'images' => 'array',
        'track_stock' => 'boolean',
        'is_available' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean'
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock_quantity', '<=', 'min_level');
    }

    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    // Mutators & Accessors
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }

            if (empty($product->code)) {
                $product->code = Str::upper(Str::random(8));
            }
        });

        static::updating(function ($product) {
            if ($product->isDirty('name')) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    // Methods
    public function getStockStatus(): string
    {
        if (!$this->track_stock) {
            return 'unlimited';
        }

        if ($this->stock_quantity <= 0) {
            return 'out_of_stock';
        }

        if ($this->stock_quantity <= $this->min_level) {
            return 'low_stock';
        }

        return 'in_stock';
    }

    public function canSell(float $quantity = 1): bool
    {
        if (!$this->is_active || !$this->is_available) {
            return false;
        }

        if ($this->track_stock && $this->stock_quantity < $quantity) {
            return false;
        }

        return true;
    }

    public function reduceStock(float $quantity, string $reason = 'Sale', ?int $userId = null): void
    {
        if (!$this->track_stock) {
            return;
        }

        $previousStock = $this->stock_quantity;
        $this->stock_quantity -= $quantity;
        $this->save();

        // Create stock movement record
        StockMovement::create([
            'product_id' => $this->id,
            'type' => 'out',
            'quantity' => $quantity,
            'previous_stock' => $previousStock,
            'new_stock' => $this->stock_quantity,
            'reason' => $reason,
            'date' => now(),
            'user_id' => $userId ?? auth()->id(),
            'company_id' => $this->company_id
        ]);
    }

    public function addStock(float $quantity, string $reason = 'Stock entry', ?int $userId = null, array $additionalData = []): void
    {
        $previousStock = $this->stock_quantity ?? 0;
        $this->stock_quantity = $previousStock + $quantity;
        $this->save();

        // Create stock movement record
        $movementData = array_merge([
            'product_id' => $this->id,
            'type' => 'in',
            'quantity' => $quantity,
            'previous_stock' => $previousStock,
            'new_stock' => $this->stock_quantity,
            'reason' => $reason,
            'date' => now(),
            'user_id' => $userId ?? auth()->id(),
            'company_id' => $this->company_id
        ], $additionalData);

        StockMovement::create($movementData);
    }

    public function getTotalSalesCount(): int
    {
        return $this->saleItems()->count();
    }

    public function getTotalRevenue(): float
    {
        return $this->saleItems()->sum('total_price');
    }


/**
 * Check if product can be sold
 */
// public function canSell(float $quantity = 1): bool
// {
//     if (!$this->is_active) {
//         return false;
//     }

//     // Check stock if tracking is enabled
//     if ($this->track_stock) {
//         return $this->stock_quantity >= $quantity;
//     }

//     return true;
// }

/**
 * Get stock status
 */
// public function getStockStatus(): string
// {
//     if (!$this->track_stock) {
//         return 'in_stock';
//     }

//     if ($this->stock_quantity <= 0) {
//         return 'out_of_stock';
//     } elseif ($this->stock_quantity <= $this->min_stock_level) {
//         return 'low_stock';
//     } else {
//         return 'in_stock';
//     }
// }

/**
 * Check if product needs restock alert
 */
public function needsRestockAlert(): bool
{
    return $this->track_stock && $this->stock_quantity <= $this->min_stock_level;
}
}
