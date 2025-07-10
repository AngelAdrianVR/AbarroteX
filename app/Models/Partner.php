<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Partner extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'earnings',
        'referrals',
        'discount_ticket_id',
    ];

    // relaciones
    public function discountTicket() :BelongsTo
    {
        return $this->belongsTo(DiscountTicket::class);
    }
}
