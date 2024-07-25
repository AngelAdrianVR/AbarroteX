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
    protected $sizeCount;

    public function __construct($codeBase, $sizeCount)
    {
        $this->storeId = auth()->user()->store_id;
        $this->codeBase = $codeBase;
        $this->sizeCount = $sizeCount;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        for ($i = 0; $i < $this->sizeCount; $i++) {
            $code = $this->codeBase . "-$i";

            // Revisa la existencia del código en la tabla 'products'
            $productQuery = DB::table('products')
                ->where('store_id', $this->storeId)
                ->where('code', $code);

            $productExists = $productQuery->exists();

            // Revisa la existencia del código en la tabla 'global_product_store'
            $globalProductQuery = GlobalProductStore::where('store_id', $this->storeId)
                ->whereHas('globalProduct', function ($query) use ($code) {
                    $query->where('code', $code);
                });

            $globalProductExists = $globalProductQuery->exists();

            // Si existe en alguna de las dos tablas, la validación falla
            if ($productExists || $globalProductExists) {
                $fail("El código '{$this->codeBase}' ya ha sido usado por otro producto. Ingresa un código diferente.");
            }
        }
    }
}
