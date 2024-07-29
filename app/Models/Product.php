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
        'currency',
        'cost',
        'code',
        'min_stock',
        'max_stock',
        'current_stock',
        'description',
        'has_inventory_control',
        'additional',
        'store_id',
        'category_id',
        'brand_id',
    ];

    protected $casts = [
        'additional' => 'array',
    ];

    //relationships
    public function history(): HasMany
    {
        return $this->hasMany(ProductHistory::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    // events
    protected static function boot()
    {
        parent::boot();

        // Definir el evento de eliminación
        static::deleting(function ($product) {
            // Obtener todas las ventas relacionadas y camviar el nombre a null para saber que se eliminó el producto
            Sale::where('product_id', $product->id)
                ->where('is_global_product', false)
                ->update(['product_id' => null]);
        });

        // Definir el evento de actualización
        static::updated(function ($product) {
            // Obtener el nombre del producto antes de la actualización
            $oldProductName = $product->getOriginal('name');

            // Obtener el nombre del producto después de la actualización
            $newProductName = $product->name;

            // Verificar si el nombre del producto ha cambiado
            if ($oldProductName !== $newProductName) {
                // Actualizar la propiedad product_name en las ventas relacionadas
                Sale::where('product_id', $product->id)
                    ->where('is_global_product', false)
                    ->update(['product_name' => $newProductName]);
            }
        });
    }
}
