<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'company', //nombre de la empresa en caso de tener
        'name', //nombre del contacto
        'phone',
        'email',
        'notes',
        'debt',
        'street',
        'suburb',
        'ext_number',
        'int_number',
        'town',
        'postal_code',
        'polity_state',
        'razon_social',
        'rfc',
        'tax_regime',
        'store_id',
    ];

    //relationships
    public function store() :BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
    
    public function sales() :HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function quotes() :HasMany
    {
        return $this->hasMany(Quote::class);
    }

    public function rentals() :HasMany
    {
        return $this->hasMany(Rental::class);
    }
}
