<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_name',
        'total',
        'products',
        'expired_date',
        'notes',
        'is_percentage_discount',
        'discount',
        'client_id',
        'store_id',
    ];

    protected $casts = [
        'expired_date' => 'date'
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
}
