<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_method',
        'amount',
        'next_payment',
        'suscription_id'
    ];  

    protected $casts = [
        'next_payment' => 'date'
    ];

    //relationships
    public function suscription() :BelongsTo
    {
        return $this->belongsTo(Suscription::class);
    }
}
