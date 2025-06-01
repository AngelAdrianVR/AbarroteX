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
        //
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
}
