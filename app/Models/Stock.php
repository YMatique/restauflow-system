<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $fillable = [
        'product_id', 'current_quantity', 'last_updated'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function entries()
    {
        return $this->hasMany(StockEntry::class, 'product_id');
    }

    public function outs()
    {
        return $this->hasMany(StockOut::class, 'product_id');
    }
}
