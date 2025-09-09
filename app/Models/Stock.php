<?php

namespace App\Models;

use App\Models\System\Company;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $fillable = [
        'name', 'status', 'notes', 'company_id',
    ];


    // Casts para facilitar manipulação
    protected $casts = [
        'status' => 'string', // poderia ser enum, mas string funciona bem
    ];


    // Relacionamentos
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function product()
    {
         return $this->hasManyThrough(
            Product::class,
            StockProduct::class,
            'stock_id',   // FK em StockProduct
            'id',         // PK do Product
            'id',         // PK do Stock
            'product_id'  // FK do StockProduct para Product
        );
    }

    // Filtro multi-tenant automático
     public function scopeForCompany($query, $companyId)
    {
        return $query->whereHas('stock', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        });
    }

     // Produtos disponíveis no armazém
    public function availableProducts()
    {
        return $this->hasManyThrough(
            Product::class,
            StockProduct::class,
            'stock_id',      // FK em StockProduct que aponta para Stock
            'id',            // PK em Product
            'id',            // PK em Stock
            'product_id'     // FK em StockProduct que aponta para Product
        )->where('status', 'available');
    }

}
