<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\GlobalProduct;
use Illuminate\Http\Request;

class GlobalProductController extends Controller
{
    
    public function selectGlobalProducts()
    {
        return inertia('GlobalProduct/SelectGlobalProducts');
    }


    public function index()
    {   
        $global_products = GlobalProduct::with(['media', 'category'])->latest()->get()->take(20);;
        $total_products = GlobalProduct::all()->count();

        // return $global_products;
        return inertia('GlobalProduct/Index', compact('global_products', 'total_products'));
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

    
    public function show($global_product_id)
    {
        $global_product = GlobalProduct::with(['media', 'category'])->find($global_product_id);
        $global_products = GlobalProduct::all(['id', 'name']);

        return inertia('GlobalProduct/Show', compact('global_product', 'global_products'));
    }

    
    public function edit($global_product_id)
    {   
        $global_product = GlobalProduct::with('media')->find($global_product_id);
        $categories = Category::all();
        
        return inertia('GlobalProduct/Edit', compact('global_product', 'categories'));
    }

    
    public function update(Request $request, GlobalProduct $global_product)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'code' => 'nullable|string|max:200',
            'public_price' => 'required|string|max:200',
            'category_id' => 'required',
        ]);

        $global_product->update($request->except('imageCover'));

        // media
        // Eliminar imagen sólo si se borró desde el input y no se agregó una nueva
        if ($request->imageCoverCleared) {
            $global_product->clearMediaCollection('imageCover');
        }

        return to_route('global_products.show', $global_product->id);
    }

    
    public function destroy(GlobalProduct $global_product)
    {
        $global_product->delete();
    }


    public function getItemsByPage($currentPage)
    {
        $offset = $currentPage * 20;
        $global_products = GlobalProduct::with('media')
            ->latest()
            ->skip($offset)
            ->take(20)
            ->get();

        return response()->json(['items' => $global_products]);
    }
}
