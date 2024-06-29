<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentalPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'paid_date',
        'amount',
        'concept',
        'notes',
        'rental_id',
        'user_id',
    ];

    protected $casts = [
        'paid_date' => 'date',
    ];

    // relaciones
    public function rental()
    {
        return $this->belongsTo(Rental::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
