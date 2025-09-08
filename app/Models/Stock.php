<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $fillable = [
        'name', 'status', 'notes', 'company_id', 
    ];

    // public function product()
    // {
    //     return $this->belongsTo(Product::class, 'product_id');
    // }

    // public function stockMoviments()
    // {
    //     return $this->hasMany(StockMovement::class)->orderBy('id', 'desc');
    // }

}
