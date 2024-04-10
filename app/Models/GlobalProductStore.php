<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GlobalProductStore extends Model
{
    use HasFactory;

    protected $fillable = [
        'public_price',
        'cost',
        'min_stock',
        'max_stock',
        'current_stock',
        'global_product_id',
        'store_id',
    ];

    //relationships
    public function globalProduct() :BelongsTo
    {
        return $this->belongsTo(GlobalProduct::class);
    }
}
