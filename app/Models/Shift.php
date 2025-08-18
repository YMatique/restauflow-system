<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $fillable = [
        'user_id', 'opened_at', 'initial_amount', 'closed_at', 'final_amount', 'status'
    ];

    /**
     * Um shift pertence a um usuário
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relações adicionais
    public function sales()
    {
        return $this->hasMany(Sale::class, 'shift_id');
    }

    public function cashMovements()
    {
        return $this->hasMany(CashMovement::class, 'shift_id');
    }
}
