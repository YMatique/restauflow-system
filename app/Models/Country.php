<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Country extends Model
{
    use HasFactory;

    protected $table = 'countries';

    protected $fillable = [
        'name',
        'code',
        'currency_code',
        'currency_name',
    ];


    protected static function booted()
    {
        // Gerar um UUID para public_id antes de criar o registro
        static::creating(function ($country) {
            if (empty($telephone->public_id)) {
                $country->public_id = Str::uuid();
            }
        });
    }

    /**
     * Relacionamento: um país tem muitas províncias
     */
    public function provinces()
    {
        return $this->hasMany(Province::class);
    }

    /**
     * Relacionamento: um país pode ter muitas empresas
     */
    public function companies()
    {
        return $this->hasMany(Company::class);
    }
}
