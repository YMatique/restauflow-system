<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventory extends Model
{

    protected $fillable = [
        'company_id',
        'reference',
        'subtotal',
        'total',
        'status',
        'user_id',
        'stock_id',
    ];

    const SIGLA = 'INV';
    const  DESCRIPTION =  'Inventory';

    // Status constants
    const STATUS_DRAFT     = 'draft';
    const STATUS_FINALIZED = 'finalized';
    const STATUS_CANCELED  = 'canceled';

    // Optional: array of all statuses
    public static array $statuses = [
        self::STATUS_DRAFT,
        self::STATUS_FINALIZED,
        self::STATUS_CANCELED,
    ];

     /**
     * Retorna todos os status para dropdown ou validação
     */
    public static function statusOptions(): array
    {
        return [
            self::STATUS_DRAFT          => __('messages.status.draft'),
            self::STATUS_FINALIZED      => __('messages.status.finalized'),
            self::STATUS_CANCELED       => __('messages.status.canceled'),
        ];
    }


    /**
     * Relacionamento com os itens do inventário
     */
    public function items(): HasMany
    {
        return $this->hasMany(InventoryItem::class);
    }

    /**
     * Get the stock related to this inventory
     */
    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class, 'stock_id');
    }


    //sCOPES
    public function getSubtotalAttribute(): float
    {
        return $this->items->sum(function($item) {
            return $item->quantity * ($item->product->price ?? 0);
        });
    }

      /**
     * Calcula o IVA (exemplo 17%)
     */
    public function getIvaAttribute(): float
    {
        $ivaPercent = 0.17;
        return $this->subtotal * $ivaPercent;
    }

    /**
     * Calcula o total do inventário
     */
    public function getTotalAttribute(): float
    {
        return $this->subtotal + $this->iva;
    }


    /**
     * Scope a query to only include inventories of a given company, ordered by id descending,
     * optionally filtered by status.
     *
     * @param  Builder      $query
     * @param  int          $companyId
     * @param  string|null  $status
     * @return Builder
     */
    public function scopeByCompany(Builder $query, int $companyId, ?string $status = null): Builder
    {
        $query->where('company_id', $companyId)
            ->orderByDesc('id');

        if ($status) {
            $query->where('status', $status);
        }

        return $query;
    }


}
