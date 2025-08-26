<?php

namespace App\Models;

use App\Models\System\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SaleItem extends Model
{
use HasFactory;

    protected $fillable = [
        'sale_id',
        'product_id',
        'product_name',
        'quantity',
        'unit_price',
        'total_price',
        'company_id'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2'
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Scopes
    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    // Methods
    public function updateQuantity(float $quantity): void
    {
        $this->update([
            'quantity' => $quantity,
            'total_price' => $this->unit_price * $quantity
        ]);

        // Recalculate sale totals
        $this->sale->calculateTotals();
    }

    public function updatePrice(float $unitPrice): void
    {
        $this->update([
            'unit_price' => $unitPrice,
            'total_price' => $unitPrice * $this->quantity
        ]);

        // Recalculate sale totals
        $this->sale->calculateTotals();
    }
}
