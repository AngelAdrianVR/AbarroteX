<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Installment extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'payment_method',
        'notes',
        'credit_sale_data_id',
        'user_id',
    ];

    // relaciones
    public function creditSaleData()
    {
        return $this->belongsTo(CreditSaleData::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    } 
}
