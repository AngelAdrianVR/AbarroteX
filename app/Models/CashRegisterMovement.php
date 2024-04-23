<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashRegisterMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'type',
        'notes',
        'cash_register_id',
    ];

    //relationships
    public function cashRegister() :BelongsTo
    {
        return $this->belongsTo(CashRegister::class);
    }
}
