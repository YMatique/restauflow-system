<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockEntry extends Model
{
    protected $fillable = [
        'product_id', 'quantity', 'date', 'supplier'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
