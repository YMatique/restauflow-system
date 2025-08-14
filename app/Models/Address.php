<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class Address extends Model
{

    protected $table = 'addresses';

    protected $fillable = [
        'public_id',
        'city',
        'country',
        'province',
        'street',
        'postalcode',
        'addressable_id',
        'addressable_type',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        // Gerar um UUID para public_id antes de criar o registro
        static::creating(function ($address) {
            if (empty($address->public_id)) {
                $address->public_id = Str::uuid();
            }
        });
    }

    /**
     * Relacionamento polimórfico: Address pode pertencer a diferentes modelos
    */
    public function addressable()
    {
        return $this->morphTo();
    }


    public function getFullAddress(): string
    {
        return "{$this->street}, {$this->city}, {$this->province}, {$this->country}, CP: {$this->postalcode}";
    }
}
