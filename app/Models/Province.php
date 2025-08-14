<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Province extends Model
{
    protected $fillable = [
        'name',
        'public_id',
        'country_id',
    ];



    // Cast do campo JSON
    protected $casts = [
        'cities' => 'array',
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
     * Relacionamento: uma província pertence a um país
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }


    /**
     * Uma província tem muitas cidades
     */
    public function cities()
    {
        return $this->hasMany(City::class);
    }
}
