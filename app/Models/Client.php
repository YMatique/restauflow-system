<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'name', 'document', 'phone', 'email', 'address'
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class, 'client_id');
    }
}
