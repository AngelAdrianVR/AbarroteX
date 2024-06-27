<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Product;
use App\Models\ProductRental;
use Illuminate\Http\Request;

class ProductRentalController extends Controller
{

    public function index()
    {
        $total_rentals = ProductRental::with(['client', 'product'])
            ->where('store_id', auth()->user()->store_id)
            ->latest()
            ->get();
        $total_product_rentals = $total_rentals->count();
        $product_rentals = $total_rentals->take(30);

        return inertia('ProductRental/Index', compact('product_rentals', 'total_product_rentals'));
    }


    public function create()
    {
        $clients = Client::where('store_id', auth()->user()->store_id)->get(['id', 'name']);
        $products = Product::where('store_id', auth()->user()->store_id)->get(['id', 'name']);

        return inertia('ProductRental/Create', compact('clients', 'products'));
    }


    public function store(Request $request)
    {
        //
    }


    public function show(ProductRental $productRental)
    {
        //
    }


    public function edit(ProductRental $productRental)
    {
        //
    }


    public function update(Request $request, ProductRental $productRental)
    {
        //
    }


    public function destroy(ProductRental $productRental)
    {
        //
    }
}
