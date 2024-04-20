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
        'current_price',
        'quantity',
        'store_id',
        // 'product_id',
        // 'global_product_store_id',
    ];

    //relationships
    public function product() :BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function globalProductStore() :BelongsTo
    {
        return $this->belongsTo(GlobalProductStore::class);
    }

    /**
     * Get the parent saleable model (product or globalProduct).
     */
    public function saleable(): MorphTo
    {
        return $this->morphTo();
    }
}
