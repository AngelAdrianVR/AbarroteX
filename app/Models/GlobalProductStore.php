<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GlobalProductStore extends Model
{
    use HasFactory;

    protected $fillable = [
        'public_price',
        'cost',
        'min_stock',
        'max_stock',
        'current_stock',
        'description',
        'has_inventory_control',
        'global_product_id',
        'store_id',
    ];

    //relationships
    public function globalProduct(): BelongsTo
    {
        return $this->belongsTo(GlobalProduct::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function history(): HasMany
    {
        return $this->hasMany(ProductHistory::class);
    }

    // events
    protected static function boot()
    {
        parent::boot();

        // Definir el evento de eliminación
        static::deleting(function ($globalProductStore) {
            // Obtener todas las ventas relacionadas
            Sale::where('product_id', $globalProductStore->id)
                ->where('is_global_product', true)
                ->update(['product_id' => null]);
        });
    }
}
