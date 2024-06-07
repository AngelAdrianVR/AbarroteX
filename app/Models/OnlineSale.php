<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnlineSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'suburb',
        'street',
        'ext_number',
        'int_number',
        'town',
        'polity_state',
        'address_references',
        'payment_method',
        'status',
        'products',
        'delivery_price',
        'total',
        'delivered_at',
        'store_id'
    ];

    protected $casts = [
        'products' => 'array',
        'delivered_at' => 'datetime',
    ];

    //relationships
    public function store() :BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
