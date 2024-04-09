<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductHistoryResource;
use App\Http\Resources\ProductResource;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Expense;
use App\Models\GlobalProductStore;
use App\Models\Product;
use App\Models\ProductHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ProductController extends Controller
{

    public function index()
    {
        // productos creados localmente en la tienda que no están en el catálogo base o global
        $local_products = Product::with(['category:id,name', 'brand:id,name', 'media'])
                ->where('store_id', auth()->user()->store_id)
                ->latest()
                ->get(['id', 'name', 'public_price', 'code', 'store_id', 'category_id', 'brand_id', 'min_stock', 'max_stock'])
                ->take(20);

        // productos transferidos desde el catálogo base
        $transfered_products = GlobalProductStore::with(['globalProduct.media'])->where('store_id', auth()->user()->store_id)->get();
        
        // Convertimos $local_products a un arreglo asociativo
        $local_products_array = $local_products->toArray();
        // Creamos un nuevo arreglo combinando los dos conjuntos de datos
        $products = new Collection(array_merge($local_products_array, $transfered_products->toArray()));

        $total_products = $products->count();

        // return $products;
        return inertia('Product/Index', compact('products', 'total_products'));
    }


    public function create()
    {
        $products_quantity = Product::all()->count();
        $categories = Category::all();
        $brands = Brand::all(['id', 'name']);
        
        return inertia('Product/Create', compact('products_quantity', 'categories', 'brands'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:products,name',
            'code' => 'nullable|unique:products,code|string|max:100',
            'public_price' => 'required|numeric|min:0|max:9999',
            'cost' => 'required|numeric|min:0|max:9999',
            'current_stock' => 'required|numeric|min:0|max:9999',
            'min_stock' => 'nullable|numeric|min:0|max:9999',
            'max_stock' => 'nullable|numeric|min:0|max:9999',
            'category_id' => 'required',
            'brand_id' => 'required',
        ]);

        $product = Product::create($request->except('imageCover') + ['store_id' => auth()->user()->store_id]);

        // Guardar el archivo en la colección 'imageCover'
        if ($request->hasFile('imageCover')) {
            $product->addMediaFromRequest('imageCover')->toMediaCollection('imageCover');
        }

        return to_route('products.show', $product->id);
    }


    public function show($product_id)
    {
        $product = ProductResource::make(Product::with('category', 'brand')->find($product_id));

        return inertia('Product/Show', compact('product'));
    }


    public function edit($product_id)
    {
        $product = ProductResource::make(Product::with('category', 'brand')->find($product_id));
        $categories = Category::all();
        $brands = Brand::all(['id', 'name']);

        return inertia('Product/Edit', compact('product', 'categories', 'brands'));
    }


    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:products,name,'.$product->id,
            'code' => 'nullable|string|max:100|unique:products,code,'.$product->id,
            'public_price' => 'required|numeric|min:0|max:9999',
            'cost' => 'required|numeric|min:0|max:9999',
            'current_stock' => 'required|numeric|min:0|max:9999',
            'min_stock' => 'nullable|numeric|min:0|max:9999',
            'max_stock' => 'nullable|numeric|min:0|max:9999',
            'category_id' => 'required',
            'brand_id' => 'required',
        ]);

        //precio actual para checar si se cambió el precio y registrarlo
        $current_price = $product->public_price;

        if ($current_price != $request->public_price) {
            ProductHistory::create([
                'description' => 'Cambio de precio de $' . $current_price . 'MXN a $ ' . $request->public_price . 'MXN.',
                'type' => 'Precio',
                'product_id' => $product->id
            ]);
        }

        $product->update($request->except('imageCover'));

        // media
        // Eliminar imágenes antiguas solo si se borró desde el input y no se agregó una nueva
        if ($request->imageCoverCleared) {
            $product->clearMediaCollection('imageCover');
        }

        return to_route('products.show', $product->id);
    }

    public function updateWithMedia(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:products,name,'.$product->id,
            'code' => 'nullable|string|max:100|unique:products,code,'.$product->id,
            'public_price' => 'required|numeric|min:0|max:9999',
            'cost' => 'required|numeric|min:0|max:9999',
            'current_stock' => 'required|numeric|min:0|max:9999',
            'min_stock' => 'nullable|numeric|min:0|max:9999',
            'max_stock' => 'nullable|numeric|min:0|max:9999',
            'category_id' => 'required',
            'brand_id' => 'required',
        ]);

        //precio actual para checar si se cambió el precio y registrarlo
        $current_price = $product->public_price;
        if ($current_price != $request->public_price) {
            ProductHistory::create([
                'description' => 'Cambio de precio de $' . $current_price . 'MXN a $ ' . $request->public_price . 'MXN.',
                'type' => 'Precio',
                'product_id' => $product->id
            ]);
        }

        $product->update($request->except('imageCover'));

        // media ------------
        // Eliminar imágenes antiguas solo si se proporcionan nuevas imágenes
        if ($request->hasFile('imageCover')) {
            $product->clearMediaCollection('imageCover');
        }

        // Guardar el archivo en la colección 'imageCover'
        if ($request->hasFile('imageCover')) {
            $product->addMediaFromRequest('imageCover')->toMediaCollection('imageCover');
        }

        return to_route('products.index');
    }


    public function destroy(Product $product)
    {
        $product->delete();
    }


    public function searchProduct(Request $request)
    {
        $query = $request->input('query');

        // Realiza la búsqueda en la base de datos
        $products = Product::with(['category', 'brand', 'media'])->where('name', 'like', "%$query%")->orWhere('code', $query)->get()->take(20);

        return response()->json(['items' => $products]);
    }


    public function getProductScaned($product_id)
    {
        // Realiza la búsqueda en la base de datos
        $product = ProductResource::make(Product::with(['category', 'brand'])->find($product_id));

        return response()->json(['item' => $product]);
    }


    public function entryStock(Request $request, $product_id)
    {
        $product = Product::find($product_id);

        // Asegúrate de convertir la cantidad a un número antes de sumar
        $product->current_stock += intval($request->quantity);

        // Guarda el producto
        $product->save();

        // Crear entrada
        ProductHistory::create([
            'description' => 'Entrada de producto. ' . $request->quantity . ' unidades',
            'type' => 'Entrada',
            'product_id' => $product_id
        ]);

        // Crear egreso
        Expense::create([
            'concept' => 'Compra de producto: ' . $product->name,
            'current_price' => $product->cost,
            'quantity' => $request->quantity
        ]);
    }


    public function fetchHistory($product_id)
    {
        $product_history = ProductHistoryResource::collection(ProductHistory::where('product_id', $product_id)->latest()->get());

        // Agrupar por mes y año
        $groupedHistory = $product_history->groupBy(function ($item) {
            return $item->created_at->format('F Y');
        });

        // Convierte el grupo en un array
        $groupedHistoryArray = $groupedHistory->toArray();

        return response()->json(['items' => $groupedHistoryArray]);
    }

    public function getItemsByPage($currentPage)
    {
        $offset = $currentPage * 20;
        $products = ProductResource::collection(Product::with(['category', 'brand'])
            ->latest()
            ->skip($offset)
            ->take(20)
            ->get());

        return response()->json(['items' => $products]);
    }
}
