<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductHistoryResource;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Expense;
use App\Models\GlobalProduct;
use App\Models\GlobalProductStore;
use App\Models\ProductHistory;
use App\Models\Sale;
use Illuminate\Http\Request;

class GlobalProductStoreController extends Controller
{

    public function index()
    {
        //
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        //
    }


    public function show($global_product_store_id)
    {
        $global_product_store = GlobalProductStore::with(['globalProduct' => ['media', 'category', 'brand']])->find($global_product_store_id);

        return inertia('GlobalProductStore/Show', compact('global_product_store'));
    }


    public function edit($global_product_store_id)
    {
        $global_product_store = GlobalProductStore::with('globalProduct.media')->find($global_product_store_id);
        $categories = Category::all();
        $brands = Brand::all(['id', 'name']);

        return inertia('GlobalProductStore/Edit', compact('global_product_store', 'categories', 'brands'));
    }


    public function update(Request $request, GlobalProductStore $global_product_store)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:100',
            'public_price' => 'required|numeric|min:0|max:9999',
            'cost' => 'nullable|numeric|min:0|max:9999',
            'current_stock' => 'required|numeric|min:0|max:9999',
            'min_stock' => 'nullable|numeric|min:0|max:9999',
            'max_stock' => 'nullable|numeric|min:0|max:9999',
            'category_id' => 'required',
            'brand_id' => 'required',
        ]);

        //precio actual para checar si se cambió el precio y registrarlo
        $current_price = $global_product_store->public_price;

        if ($current_price != $request->public_price) {
            ProductHistory::create([
                'description' => 'Cambio de precio de $' . $current_price . 'MXN a $ ' . $request->public_price . 'MXN.',
                'type' => 'Precio',
                'historicable_id' => $global_product_store->id,
                'historicable_type' => GlobalProductStore::class
            ]);
        }

        $global_product_store->update($request->except('imageCover'));

        // return to_route('global-product-store.show', $global_product_store->id); descomentar cuando este listo el show de global product store
        return to_route('products.index');
    }


    public function destroy(GlobalProductStore $global_product_store)
    {
        // indicar a la venta que el producto fue eliminado
        $related_sales = Sale::where('product_id', $global_product_store->id)->get();
        $related_sales->each(fn ($sale) => $sale->update(['product_id' => null]));

        // eliminar producto
        $global_product_store->delete();
    }


    public function entryStock(Request $request, $global_product_store_id)
    {
        $global_product_store = GlobalProductStore::with('globalProduct')->find($global_product_store_id);

        // Asegúrate de convertir la cantidad a un número antes de sumar
        $global_product_store->current_stock += intval($request->quantity);

        // Guarda el producto
        $global_product_store->save();

        // Crear entrada
        ProductHistory::create([
            'description' => 'Entrada de producto. ' . $request->quantity . ' unidades',
            'type' => 'Entrada',
            'historicable_id' => $global_product_store->id,
            'historicable_type' => GlobalProductStore::class
        ]);

        // Crear egreso
        Expense::create([
            'concept' => 'Compra de producto: ' . $global_product_store->globalProduct->name,
            'current_price' => $global_product_store->cost,
            'quantity' => $request->quantity,
            'store_id' => auth()->user()->store_id,
        ]);
    }

    public function fetchHistory($global_product_store_id, $month = null, $year = null)
    {
        // Obtener el historial filtrado por el mes y el año proporcionados, o el mes y el año actuales si no se proporcionan
        $query = ProductHistory::where('historicable_id', $global_product_store_id)
            ->where('historicable_type', GlobalProductStore::class);

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
}
