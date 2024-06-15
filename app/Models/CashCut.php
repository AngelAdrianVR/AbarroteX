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
        'store_sales_cash',
        'online_sales_cash',
        'counted_cash',
        'withdrawn_cash',
        'difference',
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
