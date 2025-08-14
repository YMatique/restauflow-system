<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class City extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'capital', 'province_id', 'public_id'];

    protected static function booted()
    {
        static::creating(function ($city) {
            if (empty($city->public_id)) {
                $city->public_id = Str::uuid();
            }
        });
    }



    /**
     * Uma cidade pertence a uma província
     */
    public function province()
    {
        return $this->belongsTo(Province::class);
    }
}
