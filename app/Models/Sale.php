<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'current_price',
        'product_name',
        'quantity',
        'product_id',
        'is_global_product',
        'cash_register_id',
        'store_id',
        'user_id',
    ];

    //relationships
    public function cashRegister() :BelongsTo
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function store() :BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function user() :BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
