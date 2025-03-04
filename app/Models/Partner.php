<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'referrals',
        'discount_ticket_id',
    ];

    // relaciones
    public function discountTicket()
    {
        return $this->belongsTo(DiscountTicket::class);
    }
}
