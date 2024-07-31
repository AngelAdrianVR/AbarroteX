<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditSaleData extends Model
{
    use HasFactory;

    protected $fillable = [
        'folio',
        'store_id',
        'expired_date',
        'status',
    ];

    protected $casts = [
        'expired_date' => 'date',
    ];

    // relaciones
    public function installments()
    {
        return $this->hasMany(Installment::class);
    }
    
    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
