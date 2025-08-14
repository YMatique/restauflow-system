<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Company extends Model
{
    protected $table = 'companies';

    protected $fillable = [
        'name',
        'nuit',
        'email',
        'avatar',
        'locale',
        'desc',
        'social_reason',
    ];


     /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        // Gerar um UUID para public_id antes de criar o registro
        static::creating(function ($company) {
            if (empty($company->public_id)) {
                $company->public_id = Str::uuid();
            }
        });
    }
}
