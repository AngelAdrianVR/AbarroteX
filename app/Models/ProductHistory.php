<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ProductHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'historicable_id',
        'historicable_type',
        'description',
        'type',
        'user_id',
        'created_at',
    ];

    //relationships
    /**
     * Get the parent historicable model (product or globalProductStore).
     */
    public function historicable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
