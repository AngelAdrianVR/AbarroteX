<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'current_price',
        'product_name',
        'quantity',
        'product_id',
        'is_global_product',
        'store_id',
    ];
}
