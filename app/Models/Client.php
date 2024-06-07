<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
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
}
