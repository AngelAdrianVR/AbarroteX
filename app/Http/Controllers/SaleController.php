<?php

namespace App\Http\Controllers;

use App\Models\GlobalProductStore;
use App\Models\Product;
use App\Models\ProductHistory;
use App\Models\Sale;
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

        return inertia('Sale/Point', compact('products'));
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
        $days_ago = Carbon::now()->subDays(5);

        // Obtener las ventas registradas en los últimos x días
        $sales = Sale::where('store_id', auth()->user()->store_id)->whereDate('created_at', '>=', $days_ago)->latest()->get();

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

        return inertia('Sale/Index', compact('groupedSales', 'total_sales'));
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        foreach ($request->data['saleProducts'] as $sale) {
            $model = isset($sale['product']['global_product_id']) ? GlobalProductStore::class : Product::class;
            //regiatra cada producto vendido
            $created_sale = Sale::create([
                'current_price' => $sale['product']['public_price'],
                'quantity' => $sale['quantity'],
                'saleable_id' => $sale['product']['id'],
                'saleable_type' => $model,
                'store_id' => auth()->user()->store_id,
            ]);

            //Registra el historial de venta de cada producto
            ProductHistory::create([
                'description' => 'Registro de venta. ' . $sale['quantity'] . ' piezas',
                'type' => 'Venta',
                'historicable_id' => $sale['product']['id'],
                'historicable_type' => $model,
            ]);

            //Desontar cantidades del stock de cada producto vendido (sólo si se configura para tomar en cuenta el inventario).
            // Verifica si 'global_product_id' existe en 'product'
            $is_inventory_on = auth()->user()->store->settings()->where('key', 'Control de inventario')->first()?->pivot->value;
            if ($is_inventory_on) {
                $product = $created_sale->saleable;
                $product->decrement('current_stock', $sale['quantity']);

                // notificar si ha llegado al limite de existencias bajas
                if ($product->current_stock <= $product->min_stock) {
                    $product_name = $model == Product::class ? $product->name : $product->globalProduct->name;
                    $title = "Bajo stock";
                    $description = "Producto <span class='text-primary'>$product_name</span> alcanzó el nivel mínimo establecido";
                    $url = route('products.show', $product->id);

                    auth()->user()->notify(new BasicNotification($title, $description, $url));
                }
            }
        }
    }


    public function show($created_at)
    {
        // Parsear la fecha recibida para obtener solo la parte de la fecha
        $date = Carbon::parse($created_at)->toDateString();

        // Obtener las ventas registradas en la fecha recibida
        $sales = Sale::where('store_id', auth()->user()->store_id)->with(['saleable'])->whereDate('created_at', $date)->get();

        // Agrupar las ventas por fecha con el nuevo formato de fecha y calcular el total de productos vendidos y el total de ventas para cada fecha
        $day_sales = $sales->groupBy(function ($sale) {
            return Carbon::parse($sale->created_at)->format('d-F-Y');
        })->map(function ($sales) {
            // Procesar las ventas
            $groupedSales = $sales->map(function ($currentSale) {
                $is_global_product = $currentSale->saleable_type == GlobalProductStore::class;
                return [
                    'id' => $currentSale->id,
                    'name' => $is_global_product 
                        ? $currentSale->saleable->globalProduct->name
                        : $currentSale->saleable->name,
                    'current_price' => $currentSale->current_price,
                    'quantity' => $currentSale->quantity,
                    'product_id' => $currentSale->saleable_id,
                    'is_global_product' => $is_global_product,
                    'created_at' => $currentSale->created_at,
                ];
            });

            // Calcular totales
            $totalQuantity = $groupedSales->sum('quantity');
            $totalSale = $groupedSales->sum(function ($productSale) {
                return $productSale['quantity'] * $productSale['current_price'];
            });

            return [
                'total_quantity' => $totalQuantity,
                'total_sale' => $totalSale,
                'sales' => $groupedSales->values(), // Convertir el mapa en un arreglo indexado
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
        $offset = 5 + $currentPage * 5; //multiplica por 5 para traer de 5 dias en 5 dias. suma 5 dias porque son los que ya se cargaron
        $skip_days = $currentPage * 5; //multiplica por 5 para traer de 5 dias en 5 dias

        // Calcular la fecha hace x días para recuperar las ventas de x dias atras hasta la fecha de hoy
        $days_ago = Carbon::now()->subDays($offset);
        // ignorar esa cantidad de dias porque ya se cargaron.
        $days_befor = Carbon::now()->subDays($skip_days);

        // Obtener las ventas registradas en los últimos 7 días
        $sales = Sale::where('store_id', auth()->user()->store_id)->whereDate('created_at', '>=', $days_ago)->whereDate('created_at', '<=', $days_befor)->latest()->get();

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

    public function printTicket($sale_id)
    {
        // $sale = SaleResource::make(Sale::with(['products'])->find($sale_id));

        // // return $sale;
        // return inertia('Sale/PrintTicket', compact('sale'));
    }
}
