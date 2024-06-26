<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditSaleData extends Model
{
    use HasFactory;

    protected $fillable = [
        'folio',
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

    public function sales()
    {
        return $this->hasMany(Sale::class, 'folio', 'folio');
    }
    
}
