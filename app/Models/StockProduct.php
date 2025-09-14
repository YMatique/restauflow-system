<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockProduct extends Model
{

    //Status possíveis
    const STATUS_AVAILABLE = 'available';
    const STATUS_RESERVED = 'reserved';
    const STATUS_DAMAGED = 'damaged';


    /**
     * Retorna todos os status para dropdown
     */
    public static function statusOptions(): array
    {
        return [
            self::STATUS_AVAILABLE  => __('messages.status.available'),
            self::STATUS_RESERVED   => __('messages.status.reserved'),
            self::STATUS_DAMAGED    => __('messages.status.damaged'),
        ];
    }


    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'stock_id',
        'product_id',
        'quantity',
        'status',
        'company_id',
    ];

    // Casts para facilitar manipulação
    protected $casts = [
        'quantity' => 'integer',
        'status' => 'string', // ou enum se preferir
    ];



    // Relacionamentos
    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Métodos de conveniência

    // Verifica se o produto está disponível
    public function isAvailable()
    {
        return $this->status === self::STATUS_AVAILABLE && $this->quantity > 0;
    }

    // Marca produto como reservado
    public function reserve($amount)
    {
        if ($this->quantity >= $amount) {
            $this->quantity -= $amount;
            $this->status = self::STATUS_RESERVED;
            $this->save();
            return true;
        }
        return false;
    }


     // Marca produto como danificado
    public function markDamaged($amount)
    {
        if ($amount <= $this->quantity) {
            $this->quantity -= $amount;
            // Se sobrar algum, mantém status available
            if ($this->quantity == 0) {
                $this->status = self::STATUS_DAMAGED;
            }
            $this->save();
            return true;
        }
        return false;
    }



    /**
     * Retorna StockProducts de um stock e produto específicos com paginação.
     * @param Stock $stock
     * @param Product $product
     * @param int $companyId
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public static function getByStockProductAndCompany(Stock $stock, Product $product, int $companyId, ?string $status = null, int $perPage = 10)
    {
        // return self::where('stock_id', $stock->id)
        //     ->where('product_id', $product->id)
        //     ->whereHas('stock', fn($q) => $q->where('company_id', $companyId))
        //     ->paginate($perPage);

            $query = self::where('stock_id', $stock->id)
        ->where('product_id', $product->id)
        ->whereHas('stock', fn($q) => $q->where('company_id', $companyId));

        // Aplica filtro por status, se fornecido
        if ($status) {
            $query->where('status', $status);
        }

        return $query->paginate($perPage);
    }


}
