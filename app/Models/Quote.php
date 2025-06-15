<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = [
        'folio',
        'contact_name',
        'phone',
        'payment_conditions',
        'email',
        'address',
        'show_iva',
        'has_discount',
        'total',
        'status', //Esperando respuesta, Autorizada, Rechazada, Pagada
        'products',
        'services',
        'expired_date',
        'notes',
        'is_percentage_discount',
        'discount',
        'client_id',
        'store_id',
    ];

    protected $casts = [
        'expired_date' => 'date',
        'products' => 'array',
        'services' => 'array'
    ];

    //relationships
    public function client() :BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function store() :BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function sale() :HasMany
    {
        return $this->hasMany(Sale::class);
    }
}
