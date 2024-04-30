<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductHistoryResource;
use App\Http\Resources\ProductResource;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Expense;
use App\Models\GlobalProduct;
use App\Models\GlobalProductStore;
use App\Models\Product;
use App\Models\ProductHistory;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class ProductController extends Controller
{

    public function index()
    {
        $all_products = $this->getAllProducts();
        $total_products = $all_products->count();

        //tomar solo 30 productos
        $products = $all_products->take(30);

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
            'name' => 'required|string|max:100|unique:products,name,' . $product->id,
            'code' => 'nullable|string|max:100|unique:products,code,' . $product->id,
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
                'historicable_id' => $product->id,
                'historicable_type' => Product::class
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
            'name' => 'required|string|max:100|unique:products,name,' . $product->id,
            'code' => 'nullable|string|max:100|unique:products,code,' . $product->id,
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
                'historicable_id' => $product->id,
                'historicable_type' => Product::class
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
        // automaticamente con un evento registrado en el modelo se actualizan las ventas relacionadas
        // eliminar producto
        $product->delete();
    }


    public function searchProduct(Request $request)
    {
        $query = $request->input('query');

        // Realiza la búsqueda en la base de datos local
        $local_products = Product::with(['category', 'brand', 'media'])
            ->where('name', 'like', "%$query%")
            ->orWhere('code', 'like', "%$query%")
            ->get();

        $global_products = GlobalProductStore::with(['globalProduct.media'])
            ->whereHas('globalProduct', function ($queryBuilder) use ($query) {
                $queryBuilder->where('name', 'like', "%$query%")
                    ->orWhere('code', $query);
            })
            ->get();

        // Combinar los resultados en una colección
        $combined_products = $local_products->merge($global_products);

        // Tomar solo los primeros 20 elementos del arreglo combinado
        $products = $combined_products;

        return response()->json(['items' => $products]);
    }


    public function getProductScaned($product_id)
    {
        $is_local_product = request()->boolean('is_local_product');

        // si es producto local busca en la tabla de productos locales, si no, en la tabla de productos transferidos del catálogo
        if ($is_local_product == '1') {
            $product = Product::with(['category', 'brand', 'media'])->find($product_id);
        } else {
            $product = GlobalProductStore::whereHas('globalProduct', function ($query) use ($product_id) {
                $query->where('id', $product_id);
            })->with(['globalProduct.category', 'globalProduct.brand', 'globalProduct.media'])->first();
        }

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
            'historicable_id' => $product_id,
            'historicable_type' => Product::class
        ]);

        // Crear egreso
        Expense::create([
            'concept' => 'Compra de producto: ' . $product->name,
            'current_price' => $product->cost,
            'quantity' => $request->quantity,
            'store_id' => auth()->user()->store_id,
        ]);
    }

    public function fetchHistory($product_id, $month = null, $year = null)
    {
        // Obtener el historial filtrado por el mes y el año proporcionados, o el mes y el año actuales si no se proporcionan
        $query = ProductHistory::where('historicable_id', $product_id)
            ->where('historicable_type', Product::class);

        if ($month && $year) {
            $query->whereMonth('created_at', $month)->whereYear('created_at', $year);
        } else {
            // Obtener el mes y el año actuales
            $currentMonth = date('m');
            $currentYear = date('Y');
            $query->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear);
        }

        // Obtener el historial ordenado por fecha de creación
        $product_history = ProductHistoryResource::collection($query->latest()->get());

        // Agrupar por mes y año
        $groupedHistory = $product_history->groupBy(function ($item) {
            return $item->created_at->format('F Y');
        });

        // Convertir el grupo en un array
        $groupedHistoryArray = $groupedHistory->toArray();

        return response()->json(['items' => $groupedHistoryArray]);
    }

    public function getItemsByPage($currentPage)
    {
        $offset = $currentPage * 30;

        // obtener todo los productos
        $all_products = $this->getAllProducts();
        $products = $all_products->splice($offset)->take(30);

        return response()->json(['items' => $products]);
    }

    public function getAllProducts()
    {
        // productos creados localmente en la tienda que no están en el catálogo base o global
        $local_products = Product::with(['category:id,name', 'brand:id,name', 'media'])
            ->where('store_id', auth()->user()->store_id)
            ->latest()
            ->get(['id', 'name', 'public_price', 'code', 'store_id', 'category_id', 'brand_id', 'min_stock', 'max_stock', 'current_stock']);

        // productos transferidos desde el catálogo base
        $transfered_products = GlobalProductStore::with(['globalProduct' => ['media', 'category']])->where('store_id', auth()->user()->store_id)->get();

        // Creamos un nuevo arreglo combinando los dos conjuntos de datos
        $merged = array_merge($local_products->toArray(), $transfered_products->toArray());
        $products = collect($merged);

        return $products;
    }
}
