<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'name',
        'public_price',
        'cost',
        'code',
        'min_stock',
        'max_stock',
        'current_stock',
        'store_id',
        'category_id',
        'brand_id',
    ];

    //relationships
    public function history() :HasMany
    {
        return $this->hasMany(ProductHistory::class);
    }

    public function sales() :HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function store() :BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function category() :BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand() :BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }
}
