<?php

namespace App\Models;

use App\Models\System\Company;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

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

    // public function products()
    // {
    //      return $this->hasManyThrough(
    //         Product::class,
    //         StockProduct::class,
    //         'stock_id',   // FK em StockProduct
    //         'id',         // PK do Product
    //         'id',         // PK do Stock
    //         'product_id'  // FK do StockProduct para Product
    //     );
    // }




     public static function getProductsSummary(int $companyId, int $perPage = 15, ?int $stockId = null): LengthAwarePaginator
    {

        $query = DB::table('stock_products as sp')
            ->join('products as p', 'p.id', '=', 'sp.product_id')
            ->select(
                'p.id',
                'p.name',
                DB::raw('SUM(sp.quantity) as total'),
                DB::raw('SUM(CASE WHEN sp.status = "available" THEN sp.quantity ELSE 0 END) as available'),
                DB::raw('SUM(CASE WHEN sp.status = "reserved" THEN sp.quantity ELSE 0 END) as reserved'),
                DB::raw('SUM(CASE WHEN sp.status = "damaged" THEN sp.quantity ELSE 0 END) as damaged')
            )
            ->where('sp.company_id', $companyId)
            // Se $stockId estiver definido, adiciona filtro
            ->when($stockId, fn($q) => $q->where('sp.stock_id', $stockId))
            ->groupBy('p.id', 'p.name');

        return $query->paginate($perPage);
    }




    public static function getFilteredStocks(int $companyId, ?string $search = null, ?string $status = null, int $perPage = 15): LengthAwarePaginator
    {
        return self::query()
            ->where('company_id', $companyId)
            ->when($search, fn(Builder $q) => $q->where('name', 'like', "%{$search}%"))
            ->when($status, fn(Builder $q) => $q->where('status', $status))
            ->paginate($perPage);
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

    public static function findStockProductId(int $productId, int $stockId, int $companyId, ?string $status = null): ?int
    {
        return self::query()
            ->where('product_id', $productId)
            ->where('stock_id', $stockId)
            ->where('company_id', $companyId)
            ->when($status, fn($q) => $q->where('status', $status))
            ->value('id');
    }

}
