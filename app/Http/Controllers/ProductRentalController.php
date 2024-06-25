<?php

namespace App\Http\Controllers;

use App\Models\ProductRental;
use Illuminate\Http\Request;

class ProductRentalController extends Controller
{
    
    public function index()
    {   
        // $product_rentals = ProductRental::where('store_id', auth()->user()->store_id)->latest()->get()->take(20);
        // $total_product_rentals = ProductRental::where('store_id', auth()->user()->store_id)->get()->count();

        return inertia('ProductRental/Index');
    }

   
    public function create()
    {
        //
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
