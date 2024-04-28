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
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function stores(): BelongsToMany
    {
        return $this->belongsToMany(GlobalProduct::class, 'global_product_stores')
            ->withPivot([
                'id',
                'public_price',
                'cost',
                'min_stock',
                'max_stock',
                'current_stock',
            ])->withTimestamps();
    }

    // events
    protected static function boot()
    {
        parent::boot();

        // Definir el evento de actualización
        static::updated(function ($globalProduct) {
            // Obtener el nombre del producto antes de la actualización
            $oldProductName = $globalProduct->getOriginal('name');

            // Obtener el nombre del producto después de la actualización
            $newProductName = $globalProduct->name;

            // Verificar si el nombre del producto ha cambiado
            if ($oldProductName !== $newProductName) {
                // obtener los ids de los productos de cada tienda creado con este producto de catalogo base
                $ids = $globalProduct->stores->map(fn ($store) => $store->pivot->id);
                // Actualizar la propiedad product_name en las ventas relacionadas
                Sale::whereIn('product_id', $ids)
                    ->where('is_global_product', true)
                    ->update(['product_name' => $newProductName]);
            }
        });
    }
}
