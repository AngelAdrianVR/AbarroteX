<?php

namespace App\Http\Controllers;

use App\Models\GlobalProductStore;
use App\Models\Product;
use App\Models\Promotions;
use Illuminate\Http\Request;

class PromotionsController extends Controller
{
    public function index()
    {
        //
    }

    public function localCreate($encoded_product_id)
    {
        // Decodificar el ID
        $decoded_product_id = base64_decode($encoded_product_id);

        $product = Product::with(['media'])->where('store_id', auth()->user()->store_id)
            ->findOrFail($decoded_product_id);

        return inertia('Promotions/Create', compact('product'));
    }

    public function globalCreate($encoded_product_id)
    {
        // Decodificar el ID
        $decoded_product_id = base64_decode($encoded_product_id);

        $product = GlobalProductStore::with(['globalProduct.media'])->where('store_id', auth()->user()->store_id)
            ->findOrFail($decoded_product_id);

        return inertia('Promotions/Create', compact('product'));
    }

    public function store(Request $request)
    {
        // Primero validamos todas las promociones
        foreach ($request->promos as $key => $promo) {
            $this->validatePromo($request, $key, $promo['type']);
        }

        // Luego, si todas las validaciones pasan, creamos las promociones
        if ($request->product_type === 'local') {
            $class = Product::class;
        } else {
            $class = GlobalProductStore::class;
        }
        foreach ($request->promos as $promo) {
            Promotions::create($promo + [
                'promotionable_id' => $request->product_id,
                'promotionable_type' => $class,
            ]);
        }

        return to_route('products.index');
    }

    public function show(Promotions $promotions)
    {
        //
    }

    public function edit(Promotions $promotions)
    {
        //
    }

    public function update(Request $request, Promotions $promotions)
    {
        //
    }

    public function destroy(Promotions $promotions)
    {
        //
    }

    public function getMatches($query)
    {
        // Realiza la búsqueda de productos locales
        $local = Product::where('name', 'like', "%{$query}%")
            ->take(10)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'type' => Product::class,
                ];
            });

        // Realiza la búsqueda de productos globales
        $global = GlobalProductStore::whereHas('globalProduct', function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%");
        })
            ->with('globalProduct') // Carga la relación para evitar N+1 queries
            ->take(10)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->globalProduct->name,
                    'type' => GlobalProductStore::class,
                ];
            });

        // Combina los resultados
        if (!$local->isEmpty()) {
            $products = $local->merge($global);
        } else {
            $products = $global->merge($local);
        }

        // Devuelve los items encontrados
        return response()->json(['items' => $products], 200);
    }

    // Método privado para manejar las validaciones
    private function validatePromo($request, $key, $type)
    {
        $rules = [];
        $messages = [];

        switch ($type) {
            case 'Descuento en precio fijo':
            case 'Descuento en porcentaje':
                $rules = [
                    "promos.$key.discounted_price" => 'required|numeric|min:0',
                ];
                $messages = [
                    'promos.*.discounted_price.required' => 'El precio promocional es obligatorio.',
                    'promos.*.discounted_price.numeric' => 'El precio promocional debe ser un número.',
                    'promos.*.discounted_price.min' => 'El precio promocional no puede ser negativo.',
                ];
                break;

            case 'Precio especial por paquete':
                $rules = [
                    "promos.$key.pack_quantity" => 'required|numeric|min:1|max:999999',
                    "promos.$key.pack_price" => 'required|numeric|min:0|max:9999999',
                ];
                $messages = [
                    'promos.*.pack_quantity.required' => 'La cantidad del paquete es obligatoria.',
                    'promos.*.pack_quantity.numeric' => 'La cantidad del paquete debe ser un número.',
                    'promos.*.pack_quantity.min' => 'La cantidad del paquete debe ser al menos 1.',
                    'promos.*.pack_price.required' => 'El precio del paquete es obligatorio.',
                    'promos.*.pack_price.numeric' => 'El precio del paquete debe ser un número.',
                    'promos.*.pack_price.min' => 'El precio del paquete no puede ser negativo.',
                ];
                break;

            case 'Promoción tipo 2x1 o 3x2':
                $rules = [
                    "promos.$key.buy_quantity" => 'required|numeric|min:1|max:999999',
                    "promos.$key.pay_quantity" => 'required|numeric|min:1|max:999999',
                ];
                $messages = [
                    'promos.*.buy_quantity.required' => 'La cantidad a comprar es obligatoria.',
                    'promos.*.buy_quantity.numeric' => 'La cantidad a comprar debe ser un número.',
                    'promos.*.buy_quantity.min' => 'La cantidad a comprar debe ser al menos 1.',
                    'promos.*.pay_quantity.required' => 'La cantidad a pagar es obligatoria.',
                    'promos.*.pay_quantity.numeric' => 'La cantidad a pagar debe ser un número.',
                    'promos.*.pay_quantity.min' => 'La cantidad a pagar debe ser al menos 1.',
                ];
                break;

            case 'Producto gratis al comprar otro':
                $rules = [
                    "promos.$key.giftable_id" => 'required',
                    "promos.$key.min_quantity_to_gift" => 'required|numeric|min:1|max:999999',
                    "promos.$key.quantity_to_gift" => 'required|numeric|min:1|max:999999',
                ];
                $messages = [
                    'promos.*.giftable_id.required' => 'El producto a regalar es obligatorio.',
                    'promos.*.min_quantity_to_gift.required' => 'La cantidad mínima para regalar es obligatoria.',
                    'promos.*.min_quantity_to_gift.numeric' => 'La cantidad mínima para regalar debe ser un número.',
                    'promos.*.min_quantity_to_gift.min' => 'La cantidad mínima para regalar debe ser al menos 1.',
                    'promos.*.quantity_to_gift.required' => 'La cantidad a regalar es obligatoria.',
                    'promos.*.quantity_to_gift.numeric' => 'La cantidad a regalar debe ser un número.',
                    'promos.*.quantity_to_gift.min' => 'La cantidad a regalar debe ser al menos 1.',
                ];
                break;
        }

        $request->validate($rules, $messages);
    }
}
