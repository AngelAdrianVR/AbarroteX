<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductHistoryResource;
use App\Models\Brand;
use App\Models\CashRegisterMovement;
use App\Models\Category;
use App\Models\Expense;
use App\Models\GlobalProduct;
use App\Models\GlobalProductStore;
use App\Models\Product;
use App\Models\ProductHistory;
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


    public function show($encoded_global_product_store_id)
    {
        // Decodificar el ID
        $decoded_global_product_id = base64_decode($encoded_global_product_store_id);

        $cash_register = auth()->user()->cashRegister;
        $global_product_store = GlobalProductStore::with(['globalProduct' => ['media', 'category', 'brand']])
            ->where('store_id', auth()->user()->store_id)
            ->findOrFail($decoded_global_product_id);

        return inertia('GlobalProductStore/Show', compact('global_product_store', 'cash_register'));
    }


    public function edit($encoded_global_product_store_id)
    {
        // Decodificar el ID
        $decoded_global_product_id = base64_decode($encoded_global_product_store_id);

        $global_product_store = GlobalProductStore::with('globalProduct.media')
            ->where('store_id', auth()->user()->store_id)
            ->findOrFail($decoded_global_product_id);
        $store = auth()->user()->store;
        $categories = Category::whereIn('business_line_name', [$store->type, $store->id])->get();
        $brands = Brand::whereIn('business_line_name', [$store->type, $store->id])->get();

        return inertia('GlobalProductStore/Edit', compact('global_product_store', 'categories', 'brands'));
    }


    public function update(Request $request, GlobalProductStore $global_product_store)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:100',
            'public_price' => 'required|numeric|min:0|max:9999',
            'cost' => 'nullable|numeric|min:0|max:9999',
            'description' => 'nullable|string|max:255',
            'current_stock' => 'required|numeric|min:0|max:9999',
            'show_in_online_store' => 'boolean',
            'min_stock' => 'nullable|numeric|min:0|max:9999',
            'max_stock' => 'nullable|numeric|min:0|max:9999',
            'category_id' => 'nullable',
            'brand_id' => 'nullable',
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

        //codifica el id del producto
        $encoded_global_product_id = base64_encode($global_product_store->id);

        return to_route('global-product-store.show', $encoded_global_product_id);
    }


    public function destroy(GlobalProductStore $global_product_store)
    {
        // automaticamente con un evento registrado en el modelo se actualizan las ventas relacionadas
        // eliminar producto
        $global_product_store->delete();
    }


    public function entryStock(Request $request, $global_product_store_id)
    {
        $messages = [
            'cash_amount.required_if' => 'El monto a retirar es obligatorio cuando el pago se realiza mediante la caja registradora.',
            'cash_amount.numeric' => 'El monto a retirar debe ser un número.',
            'cash_amount.min' => 'El monto a retirar debe ser al menos 1.',
        ];

        $request->validate([
            'quantity' => 'required|numeric|min:1',
            'is_paid_by_cash_register' => 'boolean',
            'cash_amount' => 'required_if:is_paid_by_cash_register,true|nullable|numeric|min:1',
        ], $messages);

        $global_product_store = GlobalProductStore::with('globalProduct')->find($global_product_store_id);

        // Asegúrate de convertir la cantidad a un número antes de sumar
        $global_product_store->current_stock += floatval($request->quantity);

        // Guarda el producto
        $global_product_store->save();

        // Crear entrada
        ProductHistory::create([
            'description' => 'Entrada de producto. ' . $request->quantity . ' unidades',
            'type' => 'Entrada',
            'historicable_id' => $global_product_store->id,
            'historicable_type' => GlobalProductStore::class
        ]);

        // Crear gasto
        $expense = Expense::create([
            'concept' => 'Compra de producto: ' . $global_product_store->globalProduct->name,
            'current_price' => $global_product_store->cost ?? 0,
            'quantity' => $request->quantity,
            'amount_from_cash_register' => $request->cash_amount,
            'store_id' => auth()->user()->store_id,
        ]);

        // restar de caja en caso de que el usuario asi lo haya especificado
        if ($request->is_paid_by_cash_register) {
            $unit = $request->quantity == 1 ? 'unidad' : 'unidades';
            $cash_register = auth()->user()->cashRegister;
            $cash_register->decrement('current_cash', $request->cash_amount);

            // crear movimiento de caja
            CashRegisterMovement::create([
                'amount' => $request->cash_amount,
                'type' => 'Retiro',
                'notes' => "Compra de {$global_product_store->globalProduct->name} ($request->quantity $unit)",
                'cash_register_id' => $cash_register->id,
                'expense_id' => $expense->id,
            ]);
        }
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

    public function getDataForBaseCatalogView()
    {
        //Guardar el tipo de tienda seleccionada en la vista en una variable
        $type_store = request()->store_type;
        // recupera todos los productos globales del tipo seleccionado
        $global_products = GlobalProduct::where('type', $type_store)->get(['id', 'name']);
        // recupera todos los productos de mi tienda con el tipo seleccionado
        $my_products = GlobalProductStore::with('globalProduct:id,name,type')
            ->where('store_id', auth()->user()->store_id)
            ->whereHas('globalProduct', function ($query) use ($type_store) {
                $query->where('type', $type_store);
            })
            ->get(['id', 'global_product_id']);

        $store = auth()->user()->store;
        $categories = Category::whereIn('business_line_name', [$store->type, $store->id])->get();
        $brands = Brand::whereIn('business_line_name', [$store->type, $store->id])->get();

        return response()->json(compact('global_products', 'my_products', 'categories', 'brands'));
        // return inertia('GlobalProductStore/SelectGlobalProducts', compact('global_products', 'my_products', 'categories', 'brands'));
    }

    public function transfer(Request $request)
    {
        $store = auth()->user()->store;
        // Mis productos ya registrados
        $my_products = GlobalProductStore::where('store_id', $store->id)
            ->pluck('global_product_id'); // Obtenemos solo los ids de los productos registrados

        // Obtener el arreglo de productos del cuerpo de la solicitud
        $product_ids = $request->input('products');

        // eliminar productos de la tienda que se regresaron a catalogo base
        // automaticamente con un evento registrado en el modelo se actualizan las ventas relacionadas
        GlobalProductStore::where('store_id', $store->id)
            ->whereNotIn('global_product_id', $product_ids)
            ->get()
            ->each(fn ($prd) => $prd->delete());

        // Filtrar los productos del catálogo para excluir aquellos que ya existen en mi tienda
        $new_product_ids = collect($product_ids)->reject(function ($productId) use ($my_products) {
            return $my_products->contains(function ($myProductId) use ($productId) {
                return $myProductId == $productId;
            });
        });

        // obtener prouductos totales en la tienda para establecer limite de 1500 (paquete basico)
        $total_local_products = Product::where('store_id', $store->id)->get(['id'])->count();
        $total_global_products = GlobalProductStore::where('store_id', $store->id)->get(['id'])->count();
        $total_products = $total_local_products + $total_global_products;
        $rejected_products = [];
        // agregar nuevos productos
        foreach ($new_product_ids as $productId) {
            $product = GlobalProduct::with(['category', 'brand'])->find($productId);
            // fijar un limite para paquete basico
            if ($total_products < 1500) {
                // Se obtiene el producto global con el id recibido

                if ($product) {
                    GlobalProductStore::create([
                        'public_price' => $product->public_price,
                        'cost' => 0,
                        'current_stock' => 1,
                        'min_stock' => 1,
                        'global_product_id' => $productId,
                        'store_id' => auth()->user()->store_id,
                    ]);
                }

                // agregar el producto recien creado
                $total_products++;
            } else {
                $rejected_products[] = $product->name;
            }
        }

        return response()->json(compact('rejected_products', 'total_products'));
    }

    public function changePrice(Request $request)
    {   
        // Extraer el número del string
        $idString = $request->product['id'];
        $idNumber = (int) preg_replace('/[^0-9]/', '', $idString);

        $product = GlobalProductStore::where('store_id', auth()->user()->store_id)->where('id', $idNumber)->first();
        $product->public_price = floatval($request->newPrice); //$product->public_price = (float) $request->newPrice; tambien se puede de esa manera
        $product->save();
    }
}
