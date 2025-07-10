<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{
    use HasFactory;

    protected $fillable = [
        'folio',
        'period',
        'cost',
        'status',
        'product_id',
        'client_id',
        'store_id',
        'user_id',
        'notes',
        'rented_at',
        'completed_at',
        'cancelled_at',
        'estimated_end_date',
        'estimated_end_time',
    ];

    protected $casts = [
        'period' => 'array',
        'rented_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'estimated_end_date' => 'date',
        // 'estimated_end_time' => 'datetime',
    ];

    // relaciones
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    
    public function payments()
    {
        return $this->hasMany(RentalPayment::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
