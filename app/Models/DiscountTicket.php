<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'is_percentage_discount',
        'discount_amount',
        'is_active',
        'expired_date',
    ];

    protected $casts = [
        'expired_date' => 'date'
    ];
    
}
