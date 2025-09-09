<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockProduct extends Model
{

    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'stock_id',
        'product_id',
        'quantity',
        'status',
    ];

    // Casts para facilitar manipulação
    protected $casts = [
        'quantity' => 'integer',
        'status' => 'string', // ou enum se preferir
    ];

    //Status possíveis
    const STATUS_AVAILABLE = 'available';
    const STATUS_RESERVED = 'reserved';
    const STATUS_DAMAGED = 'damaged';


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

}
