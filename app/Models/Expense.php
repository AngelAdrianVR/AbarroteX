<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'concept',
        'quantity', // cantidad del gasto
        'current_price', // costo del gasto
        'payment_method', // Efectivo, Tarjeta, Transferencia
        'store_id',
        'amount_from_cash_register', //para saber si se retiro dinero de caja y cuanto
        'created_at', // para poderponer cambiar la fecha desde el registro
    ];

    //relationships 
    public function store() :BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
    
    public function cashRegisterMovement() :HasOne
    {
        return $this->hasOne(CashRegisterMovement::class);
    }
}
