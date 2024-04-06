<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\GlobalProduct;
use Illuminate\Http\Request;

class GlobalProductController extends Controller
{
    
    public function selectGlobalProducts()
    {
        return inertia('GlobalProduct/selectGlobalProducts');
    }


    public function index()
    {   
        $global_products = GlobalProduct::latest()->get();
        return inertia('GlobalProduct/Index');
    }

    
    public function create()
    {
        $categories = Category::all();

        return inertia('GlobalProduct/Create', compact('categories'));
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'code' => 'nullable|string|max:200',
            'public_price' => 'required|string|max:200',
            'category_id' => 'required',
        ]);

        $global_product = GlobalProduct::create($request->except('imageCover'));

        // Guardar el archivo en la colección 'imageCover'
        if ($request->hasFile('imageCover')) {
            $global_product->addMediaFromRequest('imageCover')->toMediaCollection('imageCover');
        }
    }

    
    public function show(GlobalProduct $globalProduct)
    {
        //
    }

    
    public function edit(GlobalProduct $globalProduct)
    {
        //
    }

    
    public function update(Request $request, GlobalProduct $globalProduct)
    {
        //
    }

    
    public function destroy(GlobalProduct $globalProduct)
    {
        //
    }
}
