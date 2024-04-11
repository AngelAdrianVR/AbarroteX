<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'type',
        'product_id',
        'global_product_store_id',
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
}
