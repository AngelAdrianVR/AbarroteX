<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class GlobalProduct extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'name',
        'public_price',
        'code',
        'category_id',
        'brand_id',
    ];

    //relationships
    public function category() :BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand() :BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function stores() :BelongsToMany
    {
        return $this->belongsToMany(Store::class, 'global_product_store');
    }

    /**
     * Obtener ventas de este producto de catalogo base.
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Sale::class, 'saleable');
    }
}
