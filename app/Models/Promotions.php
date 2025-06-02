<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotions extends Model
{
    use HasFactory;

    public $fillable = [
        'promotionable_id',
        'promotionable_type',
        'type',
        'discount',
        'discounted_price',
        'pack_quantity',
        'pack_price',
        'giftable_id',
        'giftable_type',
        'min_quantity_to_gift',
        'quantity_to_gift',
        'buy_quantity',
        'pay_quantity',
        'is_active',
        'expiration_date',
    ];

    protected $casts = [
        'expiration_date' => 'date',
    ];

    // obtiene el modelo al que se le aplica la promoción, puede ser un producto local o global
    public function promotionable()
    {
        return $this->morphTo();
    }

    // obtiene el modelo del producto que se regala, puede ser un producto local o global
    public function giftable()
    {
        return $this->morphTo();
    }
}
