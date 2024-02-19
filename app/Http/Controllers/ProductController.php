<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductHistoryResource;
use App\Http\Resources\ProductResource;
use App\Models\Expense;
use App\Models\Product;
use App\Models\ProductHistory;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    
    public function index()
    {
        $products = ProductResource::collection(Product::get()->take(20));
        $total_products = Product::all()->count();

        return inertia('Product/Index', compact('products', 'total_products'));
    }

    
    public function create()
    {
        $products_quantity = Product::all()->count();
        return inertia('Product/Create', compact('products_quantity'));
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'nullable|unique:products|string|max:100',
            'public_price' => 'required|numeric|min:0|max:9999',
            'cost' => 'required|numeric|min:0|max:9999',
            'current_stock' => 'required|numeric|min:0|max:9999',
            'min_stock' => 'nullable|numeric|min:0|max:9999',
            'max_stock' => 'nullable|numeric|min:0|max:9999',
        ]);

        $product = Product::create($request->except('imageCover'));

        // Guardar el archivo en la colección 'guest_images'
        if ($request->hasFile('imageCover')) {
            $product->addMediaFromRequest('imageCover')->toMediaCollection('imageCover');
        }

        return to_route('products.show', $product->id);
    }

    
    public function show($product_id)
    {   
        $product = ProductResource::make(Product::find($product_id));

        return inertia('Product/Show', compact('product'));
    }

    
    public function edit($product_id)
    {
        $product = ProductResource::make(Product::find($product_id));

        return inertia('Product/Edit', compact('product'));
    }

    
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:100',
            'public_price' => 'required|numeric|min:0|max:9999',
            'cost' => 'required|numeric|min:0|max:9999',
            'current_stock' => 'required|numeric|min:0|max:9999',
            'min_stock' => 'nullable|numeric|min:0|max:9999',
            'max_stock' => 'nullable|numeric|min:0|max:9999',
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
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:100',
            'public_price' => 'required|numeric|min:0|max:9999',
            'cost' => 'required|numeric|min:0|max:9999',
            'current_stock' => 'required|numeric|min:0|max:9999',
            'min_stock' => 'nullable|numeric|min:0|max:9999',
            'max_stock' => 'nullable|numeric|min:0|max:9999',
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
        $products = ProductResource::collection(Product::where('name', 'like', "%$query%")->orWhere('code', $query)->get()->take(20));

        return response()->json(['items' => $products]);
    }


    public function getProductScaned($product_id)
    {
        // Realiza la búsqueda en la base de datos
        $product = ProductResource::make(Product::find($product_id));

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
        $products = ProductResource::collection(Product::skip($offset)
            ->take(20)
            ->get());

        return response()->json(['items' => $products]);
    }
}
