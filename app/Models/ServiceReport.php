<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ServiceReport extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    //el id 6 es de dm compresores.
    //el id * es para reparacion de celulares.

    protected $fillable = [
        'folio',
        'service_date',
        'client_name',
        'client_department',
        'client_phone_number',
        'service_description',
        'service_cost',
        'total_cost',
        'cancellation_reason',
        'status',
        'advance_payment',
        'payment_method',
        'comision_percentage',
        'product_details',
        'spare_parts',
        'observations',
        'aditionals',
        'technician_name',
        'receiver_name',
        'description',
        'store_id'
    ];

    protected $casts = [
        'service_date' => 'date',
        'product_details' => 'array',
        'spare_parts' => 'array',
        'observations' => 'array',
        'aditionals' => 'array',
    ];

    //relationships
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
