<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryItem extends Model
{
    protected $table = 'inventory_items';

    // Campos preenchíveis via mass assignment
    protected $fillable = [
        'inventory_id',
        'product_id',
        'quantity',
        'status',
        'batch_number',
        'expiry_date',
        'subtotal',
        'price'
    ];


     /**
     * Relacionamento com Product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

     /**
     * Verifica se o item está expirado
     */
    public function isExpired(): bool
    {
        return $this->expiry_date ? $this->expiry_date->isPast() : false;
    }

    /**
     * Calcula o subtotal do item (quantidade * preço do produto)
     */
    public function getSubtotalAttribute(): float
    {
        return $this->quantity * ($this->product->price ?? 0);
    }

}
