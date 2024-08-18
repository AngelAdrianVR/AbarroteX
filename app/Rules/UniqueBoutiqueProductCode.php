<?php

namespace App\Rules;

use App\Models\GlobalProductStore;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class UniqueBoutiqueProductCode implements ValidationRule
{
    protected $storeId;
    protected $codeBase;
    protected $excludedProductsName;

    public function __construct($codeBase, $excludedProductsName = null)
    {
        $this->storeId = auth()->user()->store_id;
        $this->codeBase = $codeBase;
        $this->excludedProductsName = $excludedProductsName;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Obtener los códigos únicos de productos en la tienda actual
        $productCodesQuery = DB::table('products')
            ->where('store_id', $this->storeId)
            ->select('name', 'code')
            ->when($this->excludedProductsName, function ($query) {
                $query->where('name', '<>', $this->excludedProductsName);
            })
            ->get()
            ->unique('name')
            ->pluck('code');

        // Obtener los códigos únicos de productos en la tabla global_product_store
        $globalProductCodesQuery = GlobalProductStore::where('store_id', $this->storeId)
            ->whereHas('globalProduct', function ($query) {
                $query->when($this->excludedProductsName, function ($query) {
                    $query->where('name', '<>', $this->excludedProductsName);
                });
            })
            ->get()
            ->pluck('globalProduct.code');

        // Combinar los códigos y procesarlos para obtener solo los códigos base
        $allCodes = $productCodesQuery->merge($globalProductCodesQuery)->map(function ($code) {
            return substr($code, 0, strrpos($code, '-'));
        })->unique();

        // Si el código base ya existe en la lista, la validación falla
        if ($allCodes->contains($this->codeBase)) {
            $fail("El código '{$this->codeBase}' ya ha sido usado por otro producto. Ingresa un código diferente.");
        }
    }
}
