<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DiscountTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'description',
        'is_percentage_discount',
        'discount_amount',
        'times_used',
        'is_active',
        'expired_date',
    ];

    protected $casts = [
        'expired_date' => 'date'
    ];

    // relaciones
    public function partner() :HasOne
    {
        return $this->hasOne(Partner::class);
    }
    
}
