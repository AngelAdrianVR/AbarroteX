<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Payment extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'payment_method',
        'amount',
        'suscription_period',
        'store_id',
        'status', //En revisión, Aprobado, Rechazado (status del pago)
        'invoice_status', //No solicitada, Solicitada, Enviada (status de la factura)
        'rejected_reason',
        'notes',
        'days_gifted',
        'validated_at',
        'validated_by_id',
        'created_at',
    ];

    protected $casts = ['validated_at' => 'datetime'];

    //relationships
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function validatedBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }
}
