<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'saleable_id',
        'saleable_type',
        'current_price',
        'quantity',
        'store_id',
    ];

    //relationships
    /**
     * Get the parent saleable model (product or globalProductStore).
     */
    public function saleable(): MorphTo
    {
        return $this->morphTo();
    }
}
