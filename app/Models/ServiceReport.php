<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'folio',
        'service_date',
        'client_name',
        'client_department',
        'product_details',
        'spare_parts',
        'observations',
        'technician_name',
        'receiver_name',
        'description',
        'store_id',
    ];

    protected $casts = [
        'service_date' => 'date',
        'product_details' => 'array',
        'spare_parts' => 'array',
        'observations' => 'array',
    ];

    //relationships
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
