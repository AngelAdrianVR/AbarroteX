<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\GlobalProduct;
use App\Models\GlobalProductStore;
use App\Models\Product;
use Illuminate\Http\Request;

class GlobalProductController extends Controller
{
    public function index()
    {   
        $global_products = GlobalProduct::with(['media', 'category'])->get()->take(20);;
        $total_products = GlobalProduct::all()->count();

        // return $global_products;
        return inertia('GlobalProduct/Index', compact('global_products', 'total_products'));
    }

    
    public function create()
    {
        $categories = Category::all();
        $brands = Brand::all(['id', 'name']);

        return inertia('GlobalProduct/Create', compact('categories', 'brands'));
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:global_products,name',
            'code' => 'nullable|string|max:100|unique:global_products,code',
            'public_price' => 'required|string|max:200',
            'category_id' => 'required',
            'brand_id' => 'required',
        ]);

        $global_product = GlobalProduct::create($request->except('imageCover'));

        // Guardar el archivo en la colección 'imageCover'
        if ($request->hasFile('imageCover')) {
            $global_product->addMediaFromRequest('imageCover')->toMediaCollection('imageCover');
        }
    }

    
    public function show($global_product_id)
    {
        $global_product = GlobalProduct::with(['media', 'category', 'brand'])->find($global_product_id);
        $global_products = GlobalProduct::all(['id', 'name']);

        return inertia('GlobalProduct/Show', compact('global_product', 'global_products'));
    }

    
    public function edit($global_product_id)
    {   
        $global_product = GlobalProduct::with('media')->find($global_product_id);
        $categories = Category::all();
        $brands = Brand::all(['id', 'name']);
        
        return inertia('GlobalProduct/Edit', compact('global_product', 'categories', 'brands'));
    }

    
    public function update(Request $request, GlobalProduct $global_product)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:global_products,name,'.$global_product->id,
            'code' => 'nullable|string|max:100|unique:global_products,code,'.$global_product->id,
            'public_price' => 'required|max:200',
            'category_id' => 'required',
            'brand_id' => 'required',
        ]);

        $global_product->update($request->except('imageCover'));

        // media
        // Eliminar imagen sólo si se borró desde el input y no se agregó una nueva
        if ($request->imageCoverCleared) {
            $global_product->clearMediaCollection('imageCover');
        }

        return to_route('global-products.index');
    }


    public function updateWithMedia(Request $request, GlobalProduct $global_product)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:global_products,name,'.$global_product->id,
            'code' => 'nullable|string|max:100|unique:global_products,code,'.$global_product->id,
            'public_price' => 'required|max:200',
            'category_id' => 'required',
            'brand_id' => 'required',
        ]);

        $global_product->update($request->except('imageCover'));

        // media ------------
        // Eliminar imágenes antiguas solo si se proporcionan nuevas imágenes
        if ($request->hasFile('imageCover')) {
            $global_product->clearMediaCollection('imageCover');
        }

        // Guardar el archivo en la colección 'imageCover'
        if ($request->hasFile('imageCover')) {
            $global_product->addMediaFromRequest('imageCover')->toMediaCollection('imageCover');
        }

        return to_route('global-products.index');
    }

    
    public function destroy(GlobalProduct $global_product)
    {
        $global_product->delete();
    }


    public function getItemsByPage($currentPage)
    {
        $offset = $currentPage * 20;
        $global_products = GlobalProduct::with('category', 'media', 'brand')
            ->latest()
            ->skip($offset)
            ->take(20)
            ->get();

        return response()->json(['items' => $global_products]);
    }


    public function fetchProductInfo($global_product_id)
    {
        $global_product = GlobalProduct::with('category', 'media', 'brand')->find($global_product_id);

        return response()->json(['item' => $global_product]);
    }


    public function filter(Request $request)
    {
        // Obtener los parámetros de la solicitud
        $category_id = request('category_id');
        $brand_id = request('brand_id');

        // Consultar los productos globales con los filtros aplicados
        $filtered_global_products = GlobalProduct::query();

        // Aplicar el filtro por categoría si está presente
        if ($category_id !== null) {
            $filtered_global_products->where('category_id', $category_id);
        }

        // Aplicar el filtro por marca si está presente
        if ($brand_id !== null) {
            $filtered_global_products->where('brand_id', $brand_id);
        }

        // Obtener los resultados filtrados
        $filtered_global_products = $filtered_global_products->get();

        // Devolver los resultados como una respuesta JSON
        return response()->json(['items' => $filtered_global_products]);
    }

    public function searchProduct(Request $request)
    {
        $query = $request->input('query');
    
        // Realiza la búsqueda en la base de datos local
        $global_products = GlobalProduct::with(['category', 'brand', 'media'])
            ->where('name', 'like', "%$query%")
            ->orWhere('code', $query)
            ->take(20)
            ->get();

        return response()->json(['items' => $global_products]);
    }
}
