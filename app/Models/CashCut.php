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
        'started_card',
        'started_transfer',
        'expected_cash',
        'expected_card',
        'expected_transfer',
        'store_sales_cash',
        'store_sales_card',
        'store_sales_trasnfer',
        'online_sales_cash',
        'online_sales_card',
        'online_sales_transfer',
        'service_orders_cash',
        'service_orders_card',
        'service_orders_transfer',
        'service_orders_advance_cash',
        'service_orders_advance_card',
        'service_orders_advance_transfer',
        'counted_cash',
        'counted_card',
        'counted_transfer',
        'withdrawn_cash',
        'withdrawn_card',
        'withdrawn_transfer',
        'difference_cash',
        'difference_card',
        'difference_transfer',
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
