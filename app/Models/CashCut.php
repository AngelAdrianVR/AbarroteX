<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use PhpParser\Node\Expr\FuncCall;

class CashCut extends Model
{
    use HasFactory;

    protected $fillable = [
        'started_cash',
        'expected_cash',
        'store_sales_cash', // Total de venta en tienda pagado en efectivo
        'store_sales_card', // Total de venta en tienda pagado con tarjeta
        'online_sales_cash', // Total de venta en línea pagado en efectivo
        'online_sales_card', // Total de venta en línea pagado con tarjeta
        'counted_cash', // Dinero contado manualmente de la caja
        'withdrawn_cash', // Dinero retirado de caja despues de hacer el corte
        'difference', // Diferencia entre el dinero esperado y el contado manualmente
        'notes',
        'cash_register_id',
        'store_id',
        'user_id',
    ];

    //relationships
    public Function cashRegister() :BelongsTo
    {
        return $this->belongsTo(CashRegister::class);
    }

    public Function store() :BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public Function user() :BelongsTo
    {
        return $this->belongsTo(user::class);
    }
    
}
