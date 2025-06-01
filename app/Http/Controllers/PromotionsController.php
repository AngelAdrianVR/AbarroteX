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
        foreach ($request->promos as $key => $promo) {
            if ($promo['type'] == 'Descuento en precio fijo') {
                $validated = $request->validate([
                    "promos.$key.type" => 'required|string|max:255',
                    "promos.$key.expiration_date" => 'nullable|date',
                    "promos.$key.discounted_price" => 'required|numeric|min:0',
                ], [
                    'promos.*.discounted_price.required' => 'El precio promocional es obligatorio.',
                    'promos.*.discounted_price.numeric' => 'El precio promocional debe ser un número.',
                    'promos.*.discounted_price.min' => 'El precio promocional no puede ser negativo.',
                ]);
            }
        }
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
}
