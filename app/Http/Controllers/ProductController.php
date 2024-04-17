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
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

class ProductController extends Controller
{

    public function index()
    {
        // productos creados localmente en la tienda que no están en el catálogo base o global
        $local_products = Product::with(['category:id,name', 'brand:id,name', 'media'])
            ->where('store_id', auth()->user()->store_id)
            ->latest()
            ->get(['id', 'name', 'public_price', 'code', 'store_id', 'category_id', 'brand_id', 'min_stock', 'max_stock', 'current_stock']);

        // productos transferidos desde el catálogo base
        $transfered_products = GlobalProductStore::with(['globalProduct' => ['media', 'category']])->where('store_id', auth()->user()->store_id)->get();

        // Convertimos $local_products a un arreglo asociativo
        $local_products_array = $local_products->toArray();


        // Creamos un nuevo arreglo combinando los dos conjuntos de datos
        $products = new Collection(array_merge($local_products_array, $transfered_products->toArray()));

        $total_products = $products->count();

        //tomar solo 30 productos
        $products = $products->take(30);

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

        // Realiza la búsqueda en la base de datos local
        $local_products = Product::with(['category', 'brand', 'media'])
            ->where('name', 'like', "%$query%")
            ->orWhere('code', $query)
            ->take(20)
            ->get();

        $global_products = GlobalProductStore::with(['globalProduct.media'])
            ->whereHas('globalProduct', function (Builder $queryBuilder) use ($query) {
                $queryBuilder->where('name', 'like', "%$query%")
                    ->orWhere('code', $query);
            })
            ->take(20)
            ->get();

        // Combinar los resultados en una colección
        $combined_products = $local_products->merge($global_products);

        // Tomar solo los primeros 20 elementos del arreglo combinado
        $products = $combined_products->take(20);

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
            'product_id' => $product_id
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
        $query = ProductHistory::where('product_id', $product_id);

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
        //si es la primera consulta cuenta cuantos productos locales existen para tomarlo
        //en cuenta para el offset de la siguiente consulta
        if ($currentPage === '1') {
            $local_products = Product::where('store_id', auth()->user()->store_id)
                ->get(['id', 'name'])
                ->count();

            // si hay mas de 30 productos locales 
            if ($local_products > 30) {
                $offset = $currentPage * 30;
                $products = Product::with(['category:id,name', 'brand:id,name', 'media'])
                    ->where('store_id', auth()->user()->store_id)
                    ->latest()
                    ->skip($offset)
                    ->get((['id', 'name', 'public_price', 'code', 'store_id', 'category_id', 'brand_id', 'min_stock', 'max_stock', 'current_stock']));
            }

            $offset = ($currentPage * 30) - $local_products;
        } else {

            $offset = $currentPage * 30;
        }
        $products = GlobalProductStore::with(['globalProduct' => ['media', 'category']])
            ->where('store_id', auth()->user()->store_id)
            ->latest()
            ->skip($offset)
            ->take(30)
            ->get();

        return response()->json(['items' =>  $products]);
    }

    public function selectGlobalProducts()
    {
        $global_products = GlobalProduct::all(['id', 'name']);
        $my_products = GlobalProductStore::with('globalProduct:id,name')->where('store_id', auth()->user()->store_id)->get(['id', 'global_product_id']);
        $categories = Category::all(['id', 'name']);
        $brands = Brand::all(['id', 'name']);

        return inertia('Product/SelectGlobalProducts', compact('global_products', 'my_products', 'categories', 'brands'));
    }

    public function transfer(Request $request)
    {
        // Mis productos ya registrados
        $my_products = GlobalProductStore::where('store_id', auth()->user()->store_id)
            ->pluck('global_product_id'); // Obtenemos solo los ids de los productos registrados

        // Obtener el arreglo de productos del cuerpo de la solicitud
        $product_ids = $request->input('products');

        // Filtrar los productos del catálogo para excluir aquellos que ya existen en mi tienda
        $new_product_ids = collect($product_ids)->reject(function ($productId) use ($my_products) {
            return $my_products->contains(function ($myProductId) use ($productId) {
                return $myProductId == $productId;
            });
        });

        // Aquí puedes manipular el arreglo de productos como desees
        // Por ejemplo, puedes iterar sobre el arreglo y hacer lo que necesites
        foreach ($new_product_ids as $productId) {
            // Se obtiene el producto global con el id recibido
            $product = GlobalProduct::with(['category', 'brand'])->find($productId);

            GlobalProductStore::create([
                'public_price' => $product->public_price,
                'cost' => 0,
                'current_stock' => 1,
                'min_stock' => 1,
                'global_product_id' => $productId,
                'store_id' => auth()->user()->store_id,
            ]);
        }

        return to_route('products.index');
    }
}
