<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class Telephone extends Model
{
    /** @use HasFactory<\Database\Factories\TelephoneFactory> */
    use HasFactory;


    protected $fillable = ['format', 'number', 'type', 'is_primary', 'public_id'];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */

    protected static function booted()
    {
        // Gerar um UUID para public_id antes de criar o registro
        static::creating(function ($telephone) {
            if (empty($telephone->public_id)) {
                $telephone->public_id = Str::uuid();
            }
        });
    }


    public function telephonable(){
        return $this->morphTo();
    }


    public function getFullNumberAttribute(){
        return "{$this->format} {$this->number}";
    }
}
