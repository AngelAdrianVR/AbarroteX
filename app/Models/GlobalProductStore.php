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

            // modifica tambien las ventas en linea ------------------------------------------
            // Obtener todas las ventas en línea relacionadas
            $online_sales = OnlineSale::where('store_id', auth()->user()->store_id)->get();

            foreach ($online_sales as $sale) {
                // Iterar sobre los productos en cada venta
                $products = $sale->products;
                foreach ($products as &$online_product) {
                    if ($online_product['product_id'] === $globalProductStore->id) {
                        // Cambiar el valor del product_id a null para indicar que el producto fue eliminado
                        $online_product['product_id'] = null;
                    }
                }
                // Guardar los cambios en los productos de la venta
                $sale->products = $products;
                $sale->save();
            }
        });
    }
}
