<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\GlobalProduct;
use App\Models\GlobalProductStore;
use App\Models\Product;
use App\Models\ProductHistory;
use App\Models\Sale;
use App\Models\User;
use App\Notifications\BasicNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function pointIndex()
    {
        // productos creados localmente en la tienda que no están en el catálogo base o global
        $local_products = Product::where('store_id', auth()->user()->store_id)
            ->latest()
            ->get(['id', 'name', 'code']);

        // productos transferidos desde el catálogo base
        $transfered_products = GlobalProductStore::with(['globalProduct:id,name,code'])
            ->where('store_id', auth()->user()->store_id)
            ->get(['id', 'global_product_id']);

        // Convertimos $local_products a un arreglo asociativo
        $local_products_array = $local_products->toArray();

        // Creamos un nuevo arreglo combinando los dos conjuntos de datos
        $products = new Collection(array_merge($local_products_array, $transfered_products->toArray()));

        //recupera la primera caja registradora de la tienda para mandar su info como current_cash
        $cash_register = CashRegister::where('store_id', auth()->user()->store_id)->first();

        return inertia('Sale/Point', compact('products', 'cash_register'));
    }


    public function index()
    {
        // Obtener todos las ventas registradas y contar el número de agrupaciones por día
        $total_sales = DB::table('sales')
            ->select(DB::raw('DATE(created_at) as date'))
            ->where('store_id', auth()->user()->store_id)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get()
            ->count();

        // Calcular la fecha hace x días para recuperar las ventas de x dias atras hasta la fecha de hoy
        // $days_ago = Carbon::now()->subDays(5);
        // Obtener las ventas registradas en los últimos x días
        // $sales = Sale::where('store_id', auth()->user()->store_id)->whereDate('created_at', '>=', $days_ago)->latest()->get();

        $sales = Sale::where('store_id', auth()->user()->store_id)->latest()->get();

        // Agrupar las ventas por fecha con el nuevo formato de fecha y calcular el total de productos vendidos y el total de ventas para cada fecha
        $groupedSales = $sales->groupBy(function ($sale) {
            return Carbon::parse($sale->created_at)->format('d-F-Y');
        })->map(function ($sales) {
            $totalQuantity = $sales->sum('quantity');
            $totalSale = $sales->sum(function ($sale) {
                return $sale->quantity * $sale->current_price;
            });

            return [
                'total_quantity' => $totalQuantity,
                'total_sale' => $totalSale,
                'sales' => $sales,
            ];
        })->take(30);

        return inertia('Sale/Index', compact('groupedSales', 'total_sales'));
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        //recorre el arreglo de productos registrados en la venta.
        $this->storeEachProductSold($request->data['saleProducts']);

        // // obtiene la primera caja registradora de la tienda
        // $cash_register = CashRegister::where('store_id', auth()->user()->store_id)->first();

        // //recorre el arreglo de productos.
        // foreach ($request->data['saleProducts'] as $sale) {
        //     $is_global_product = isset($sale['product']['global_product_id']);

        //     $product_name = $is_global_product
        //         ? GlobalProductStore::find($sale['product']['id'])->globalProduct->name
        //         : Product::find($sale['product']['id'])->name;

        //     //regiatra cada producto vendido
        //     Sale::create([
        //         'current_price' => $sale['product']['public_price'],
        //         'quantity' => $sale['quantity'],
        //         'product_name' => $product_name,
        //         'product_id' => $sale['product']['id'],
        //         'is_global_product' => $is_global_product,
        //         'store_id' => auth()->user()->store_id,
        //     ]);

        //     //Suma la cantidad total de dinero vendido del producto al dinero actual de la caja
        //     $cash_register->current_cash += $sale['product']['public_price'] * $sale['quantity'];
        //     $cash_register->save();

        //     //Registra el historial de venta de cada producto
        //     ProductHistory::create([
        //         'description' => 'Registro de venta. ' . $sale['quantity'] . ' piezas',
        //         'type' => 'Venta',
        //         'historicable_id' => $sale['product']['id'],
        //         'historicable_type' => $is_global_product
        //             ? GlobalProductStore::class
        //             : Product::class,
        //     ]);

        //     //Desontar cantidades del stock de cada producto vendido (sólo si se configura para tomar en cuenta el inventario).
        //     // Verifica si 'global_product_id' existe en 'product'
        //     $is_inventory_on = auth()->user()->store->settings()->where('key', 'Control de inventario')->first()?->pivot->value;
        //     if ($is_inventory_on) {
        //         $product = $is_global_product
        //             ? GlobalProductStore::find($sale['product']['id'])
        //             : Product::find($sale['product']['id']);

        //         $product->decrement('current_stock', $sale['quantity']);

        //         // notificar si ha llegado al limite de existencias bajas
        //         if ($product->current_stock <= $product->min_stock) {
        //             $title = "Bajo stock";
        //             $description = "Producto <span class='text-primary'>$product_name</span> alcanzó el nivel mínimo establecido";
        //             $url = route('products.show', $product->id);

        //             auth()->user()->notify(new BasicNotification($title, $description, $url));
        //         }
        //     }
        // }
    }


    public function show($created_at)
    {
        // Parsear la fecha recibida para obtener solo la parte de la fecha
        $date = Carbon::parse($created_at)->toDateString();

        // Obtener las ventas registradas en la fecha recibida
        $sales = Sale::where('store_id', auth()->user()->store_id)->whereDate('created_at', $date)->get();

        // Agrupar las ventas por fecha con el nuevo formato de fecha y calcular el total de productos vendidos y el total de ventas para cada fecha
        $day_sales = $sales->groupBy(function ($sale) {
            return Carbon::parse($sale->created_at)->format('d-F-Y');
        })->map(function ($sales) {
            // Calcular totales
            $totalQuantity = $sales->sum('quantity');
            $totalSale = $sales->sum(function ($productSale) {
                return $productSale['quantity'] * $productSale['current_price'];
            });

            return [
                'total_quantity' => $totalQuantity,
                'total_sale' => $totalSale,
                'sales' => $sales->values(), // Convertir el mapa en un arreglo indexado
            ];
        });

        return inertia('Sale/Show', compact('day_sales'));
    }


    public function edit(Sale $sale)
    {
        //
    }


    public function update(Request $request, Sale $sale)
    {
        //
    }


    public function destroy(Sale $sale)
    {
        // Obtener la fecha de creación del registro de venta
        $saleDate = $sale->created_at->toDateString();

        // Eliminar todos los registros que tengan la misma fecha de creación
        Sale::where('store_id', auth()->user()->store_id)->whereDate('created_at', $saleDate)->delete();

        // Eliminar el registro de venta enviado como referencia
        $sale->delete();
    }


    public function searchProduct(Request $request)
    {
        $queryDate = $request->input('queryDate');
        $startDate = Carbon::parse($queryDate[0])->startOfDay();
        $endDate = Carbon::parse($queryDate[1])->endOfDay();

        // Obtener las ventas registradas en el rango de fechas requerido por el filtro
        $sales = Sale::where('store_id', auth()->user()->store_id)->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate)->latest()->get();

        // Agrupar las ventas por fecha con el nuevo formato de fecha y calcular el total de productos vendidos y el total de ventas para cada fecha
        $groupedSales = $sales->groupBy(function ($sale) {
            return Carbon::parse($sale->created_at)->format('d-F-Y');
        })->map(function ($sales) {
            $totalQuantity = $sales->sum('quantity');
            $totalSale = $sales->sum(function ($sale) {
                return $sale->quantity * $sale->current_price;
            });

            return [
                'total_quantity' => $totalQuantity,
                'total_sale' => $totalSale,
                'sales' => $sales,
            ];
        });

        return response()->json(['items' => $groupedSales]);
    }


    public function getItemsByPage($currentPage)
    {
        $offset = $currentPage * 30;
        // Calcular la fecha hace x días para recuperar las ventas de x dias atras hasta la fecha de hoy
        // $days_ago = Carbon::now()->subDays($offset);
        // ignorar esa cantidad de dias porque ya se cargaron.
        // $days_befor = Carbon::now()->subDays($skip_days);
        // Obtener las ventas registradas en los últimos 7 días
        // $sales = Sale::where('store_id', auth()->user()->store_id)->whereDate('created_at', '>=', $days_ago)->whereDate('created_at', '<=', $days_befor)->latest()->get();
        $sales = Sale::where('store_id', auth()->user()->store_id)->latest()->get();

        // Agrupar las ventas por fecha con el nuevo formato de fecha y calcular el total de productos vendidos y el total de ventas para cada fecha
        $groupedSales = $sales->groupBy(function ($sale) {
            return Carbon::parse($sale->created_at)->format('d-F-Y');
        })->map(function ($sales) {
            $totalQuantity = $sales->sum('quantity');
            $totalSale = $sales->sum(function ($sale) {
                return $sale->quantity * $sale->current_price;
            });

            return [
                'total_quantity' => $totalQuantity,
                'total_sale' => $totalSale,
                'sales' => $sales,
            ];
        })->skip($offset)
            ->take(30);

        return response()->json(['items' => $groupedSales]);
    }

    public function printTicket($created_at)
    {
        // Parsear la fecha recibida para obtener solo la parte de la fecha
        $date = Carbon::parse($created_at)->toDateString();

        // Obtener las ventas registradas en la fecha recibida
        $sales = Sale::where('store_id', auth()->user()->store_id)->whereDate('created_at', $date)->get();

        // Agrupar las ventas por fecha con el nuevo formato de fecha y calcular el total de productos vendidos y el total de ventas para cada fecha
        $day_sales = $sales->groupBy(function ($sale) {
            return Carbon::parse($sale->created_at)->format('d-F-Y');
        })->map(function ($sales) {
            // Calcular totales
            $totalQuantity = $sales->sum('quantity');
            $totalSale = $sales->sum(function ($productSale) {
                return $productSale['quantity'] * $productSale['current_price'];
            });

            return [
                'total_quantity' => $totalQuantity,
                'total_sale' => $totalSale,
                'sales' => $sales->values(), // Convertir el mapa en un arreglo indexado
            ];
        });

        // return $day_sales;
        return inertia('Sale/PrintTicket', compact('day_sales'));
    }

    public function syncLocalstorage(Request $request)
    {
        //recorre el arreglo de ventas registradas.
        foreach ($request->sales as $sale) {
            //recorre el arreglo de productos registrados en la venta.
            $this->storeEachProductSold($sale['saleProducts'], $sale['created_at']);
        }
    }

    private function storeEachProductSold($sold_products, $created_at = null)
    {
        // obtiene la primera caja registradora de la tienda
        $cash_register = CashRegister::where('store_id', auth()->user()->store_id)->first();

        foreach ($sold_products as $product) {
            $is_global_product = isset($product['product']['global_product_id']);

            $product_name = $is_global_product
                ? GlobalProductStore::find($product['product']['id'])->globalProduct->name
                : Product::find($product['product']['id'])->name;

            //regiatra cada producto vendido
            Sale::create([
                'current_price' => $product['product']['public_price'],
                'quantity' => $product['quantity'],
                'product_name' => $product_name,
                'product_id' => $product['product']['id'],
                'is_global_product' => $is_global_product,
                'store_id' => auth()->user()->store_id,
                'created_at' => $created_at ?? now(),
            ]);

            //Suma la cantidad total de dinero vendido del producto al dinero actual de la caja
            $cash_register->current_cash += $product['product']['public_price'] * $product['quantity'];
            $cash_register->save();

            //Registra el historial de venta de cada producto
            ProductHistory::create([
                'description' => 'Registro de venta. ' . $product['quantity'] . ' piezas',
                'type' => 'Venta',
                'historicable_id' => $product['product']['id'],
                'historicable_type' => $is_global_product
                    ? GlobalProductStore::class
                    : Product::class,
                'created_at' => $created_at ?? now(),
            ]);

            //Desontar cantidades del stock de cada producto vendido (sólo si se configura para tomar en cuenta el inventario).
            // Verifica si 'global_product_id' existe en 'product'
            $is_inventory_on = auth()->user()->store->settings()->where('key', 'Control de inventario')->first()?->pivot->value;
            if ($is_inventory_on) {
                $product = $is_global_product
                    ? GlobalProductStore::find($product['product']['id'])
                    : Product::find($product['product']['id']);

                $product->decrement('current_stock', $product['quantity']);

                // notificar si ha llegado al limite de existencias bajas
                if ($product->current_stock <= $product->min_stock) {
                    $title = "Bajo stock";
                    $description = "Producto <span class='text-primary'>$product_name</span> alcanzó el nivel mínimo establecido";
                    $url = route('products.show', $product->id);

                    auth()->user()->notify(new BasicNotification($title, $description, $url));
                }
            }
        }
    }
}
