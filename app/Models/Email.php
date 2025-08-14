<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    protected $table = 'emails';

    protected $fillable = [];

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

    public function emailable()
    {
        return $this->morphTo();
    }


}
