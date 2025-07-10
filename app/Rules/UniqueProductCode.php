<?php

namespace App\Rules;

use App\Models\GlobalProductStore;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class UniqueProductCode implements ValidationRule
{
    protected $storeId;
    protected $productId;

    public function __construct($productId = null)
    {
        $this->storeId = auth()->user()->store_id;
        $this->productId = $productId;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Revisa la existencia del código en la tabla 'products'
        $productQuery = DB::table('products')
            ->where('store_id', $this->storeId)
            ->where('code', $value);

        if ($this->productId) {
            $productQuery->where('id', '!=', $this->productId);
        }

        $productExists = $productQuery->exists();

        // Revisa la existencia del código en la tabla 'global_product_store'
        $globalProductQuery = GlobalProductStore::where('store_id', $this->storeId)
            ->whereHas('globalProduct', function ($query) use ($value) {
                $query->where('code', $value);
            });

        if ($this->productId) {
            $globalProductQuery->where('id', '!=', $this->productId);
        }

        $globalProductExists = $globalProductQuery->exists();

        // Si existe en alguna de las dos tablas, la validación falla
        if ($productExists || $globalProductExists) {
            $fail('El código ya ha sido usado por otro producto. Ingresa uno diferente');
        }
    }
}
