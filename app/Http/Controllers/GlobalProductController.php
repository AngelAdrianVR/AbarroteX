<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\GlobalProduct;
use Illuminate\Http\Request;

class GlobalProductController extends Controller
{
    public function index()
    {
        $global_products = GlobalProduct::with(['media', 'category'])->get()->take(50);
        $total_products = GlobalProduct::all()->count();

        return inertia('GlobalProduct/Index', compact('global_products', 'total_products'));
    }


    public function create()
    {
        $store = auth()->user()->store;
        $categories = Category::whereIn('business_line_name', [$store->type, $store->id])->get();
        $brands = Brand::whereIn('business_line_name', [$store->type, $store->id])->get();

        return inertia('GlobalProduct/Create', compact('categories', 'brands'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:global_products,name',
            'code' => 'nullable|string|max:100|unique:global_products,code',
            'descrition' => 'nullable|string|max:255',
            'public_price' => 'required|string|max:200',
            'category_id' => 'nullable',
            'brand_id' => 'nullable',
        ]);

        $global_product = GlobalProduct::create($request->except('imageCover') + ['type' => auth()->user()->store->type]);

        // Guardar el archivo en la colección 'imageCover'
        if ($request->hasFile('imageCover')) {
            $global_product->addMediaFromRequest('imageCover')->toMediaCollection('imageCover');
        }
    }


    public function show($global_product_id)
    {
        $global_product = GlobalProduct::with(['media', 'category', 'brand'])->findOrFail($global_product_id);
        $global_products = GlobalProduct::all(['id', 'name']);

        return inertia('GlobalProduct/Show', compact('global_product', 'global_products'));
    }


    public function edit($global_product_id)
    {
        $global_product = GlobalProduct::with('media')->findOrFail($global_product_id);
        $store = auth()->user()->store;
        $categories = Category::whereIn('business_line_name', [$store->type, $store->id])->get();
        $brands = Brand::whereIn('business_line_name', [$store->type, $store->id])->get();

        return inertia('GlobalProduct/Edit', compact('global_product', 'categories', 'brands'));
    }


    public function update(Request $request, GlobalProduct $global_product)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:global_products,name,' . $global_product->id,
            'code' => 'nullable|string|max:100|unique:global_products,code,' . $global_product->id,
            'descrition' => 'nullable|string|max:255',
            'public_price' => 'required|max:200',
            'category_id' => 'nullable',
            'brand_id' => 'nullable',
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
            'name' => 'required|string|max:100|unique:global_products,name,' . $global_product->id,
            'code' => 'nullable|string|max:100|unique:global_products,code,' . $global_product->id,
            'descrition' => 'nullable|string|max:255',
            'public_price' => 'required|max:200',
            'category_id' => 'nullable',
            'brand_id' => 'nullable',
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
        $offset = $currentPage * 50;
        $global_products = GlobalProduct::with('category', 'media', 'brand')
            ->latest()
            ->get()
            ->splice($offset)
            ->take(50);

        return response()->json(['items' => $global_products]);
    }


    public function fetchProductInfo($global_product_id)
    {
        $global_product = GlobalProduct::with('category', 'media', 'brand')->find($global_product_id);

        return response()->json(['item' => $global_product]);
    }


    public function filter(Request $request)
    {
        $category_ids = $request->input('category_ids', []);
        $brand_ids = $request->input('brand_ids', []);

        $query = GlobalProduct::query();

        // Filtro por múltiples categorías
        if (!empty($category_ids)) {
            $query->whereIn('category_id', $category_ids);
        }

        // Filtro por múltiples marcas
        if (!empty($brand_ids)) {
            $query->whereIn('brand_id', $brand_ids);
        }

        $filtered_global_products = $query->get();

        return response()->json(['items' => $filtered_global_products]);
    }

    public function searchProduct(Request $request)
    {
        $query = $request->input('query');
        $bussiness_line = auth()->user()->store->type;

        if ( $request->input('module') === 'base_catalog' ) {
            // Realiza la búsqueda en la base de datos para catalogo base
            $global_products = GlobalProduct::where('name', 'like', "%$query%")
                ->where('type', $bussiness_line)
                ->orWhere('code', $query)
                ->get();
        } else {
            // Realiza la búsqueda en la base de datos local con media, categoría y marca para index de catalogo de productos
            $global_products = GlobalProduct::with(['category', 'brand', 'media'])
                ->where('name', 'like', "%$query%")
                ->orWhere('code', $query)
                ->take(20)
                ->get();
        }

        return response()->json(['items' => $global_products]);
    }
}
