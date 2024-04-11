<?php

namespace App\Http\Controllers;

use App\Http\Resources\SaleResource;
use App\Models\GlobalProductStore;
use App\Models\Product;
use App\Models\ProductHistory;
use App\Models\Sale;
use App\Notifications\BasicNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class SaleController extends Controller
{
    public function pointIndex()
    {
        // $products = Product::all(['id', 'name', 'code']);

        // productos creados localmente en la tienda que no están en el catálogo base o global
        $local_products = Product::where('store_id', auth()->user()->store_id)
                ->latest()
                ->get(['id', 'name','code']);

        // productos transferidos desde el catálogo base
        $transfered_products = GlobalProductStore::with(['globalProduct:id,name,code'])->where('store_id', auth()->user()->store_id)->get(['id','global_product_id']);
        
        // Convertimos $local_products a un arreglo asociativo
        $local_products_array = $local_products->toArray();

        // Creamos un nuevo arreglo combinando los dos conjuntos de datos
        $products = new Collection(array_merge($local_products_array, $transfered_products->toArray()));

        // return $products;
        return inertia('Sale/Point', compact('products'));
    }

    
    public function index()
    {
         // Calcular la fecha hace x días para recuperar las ventas de x dias atras hasta la fecha de hoy
        $days_ago = Carbon::now()->subDays(5);

        // Obtener las ventas registradas en los últimos 7 días
        $sales = Sale::whereDate('created_at', '>=', $days_ago)->latest()->get();

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

        // return $groupedSales;
        return inertia('Sale/Index', compact('groupedSales'));
    }

    
    public function create()
    {
        //
    }

    
    public function store(Request $request)
    {   
        // return $request;
        foreach ($request->data['saleProducts'] as $sale) {

             // Verifica si 'global_product_id' existe en 'product'
            if (isset($sale['product']['global_product_id'])) {
                // Si existe, asigna el valor de 'global_product_id'
                $product_id = $sale['product']['global_product_id'];
            } else {
                // Si no existe, asigna el valor de 'id' dentro de 'product'
                $product_id = $sale['product']['id'];
            }

            //regiatra cada producto vendido
            Sale::create([
                'current_price' => $sale['product']['public_price'],
                'quantity' => $sale['quantity'],
                'product_id' => $product_id,
                'store_id' => auth()->user()->store_id,
            ]);

            //Registra el historial de venta de cada producto
            // ProductHistory::create([
            //     'description' => 'Registro de venta. ' . $sale['quantity'] . ' piezas',
            //     'type' => 'Venta',
            //     'product_id' => $sale['product']['id']
            // ]);

            //rebaja del stock la cantidad de piezas vendidas
            // $product = Product::find($sale['product']['id']);
            // $product->decrement('current_stock', $sale['quantity']);

            // notificar si ha llegado al limite de existencias bajas
            // if ($product->current_stock <= $product->min_stock) {
            //     $title = "Bajo stock";
            //     $description = "Producto <span class='text-primary'>$product->name</span> alcanzó el nivel mínimo establecido";
            //     $url = route('products.show', $product->id);

            //     auth()->user()->notify(new BasicNotification($title, $description, $url));
            // }

        }
    }

    
    public function show($created_at)
    {
        
    // Parsear la fecha recibida para obtener solo la parte de la fecha
    $date = Carbon::parse($created_at)->toDateString();

    // Obtener las ventas registradas en la fecha recibida
    $sales = Sale::with('product:id,name')->whereDate('created_at', $date)->get();

    // Agrupar las ventas por fecha con el nuevo formato de fecha y calcular el total de productos vendidos y el total de ventas para cada fecha
    $day_sales = $sales->groupBy(function ($sale) {
        return Carbon::parse($sale->created_at)->format('d-F-Y');
    })->map(function ($sales) {
        // Agrupar las ventas por product_id y sumar la cantidad vendida para cada producto
        $groupedSales = $sales->groupBy('product_id')->map(function ($productSales) {
            return [
                'id' => $productSales->first()->id,
                'name' => $productSales->first()->product?->name,
                'current_price' => $productSales->first()->current_price,
                'quantity' => $productSales->sum('quantity'),
                'product_id' => $productSales->first()->product_id,
                'created_at' => $productSales->first()->created_at,
            ];
        });
    
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

        // return $day_sales;
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
        Sale::whereDate('created_at', $saleDate)->delete();

        // Eliminar el registro de venta enviado como referencia
        $sale->delete();
    }


    public function searchProduct(Request $request)
    {
        $queryDate = $request->input('queryDate');
        $startDate = Carbon::parse($queryDate[0])->startOfDay();
        $endDate = Carbon::parse($queryDate[1])->endOfDay();

        // Obtener las ventas registradas en los últimos 7 días
        $sales = Sale::whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate)->latest()->get();

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

        // // Filtrar por rango de fechas si se proporciona
        // if (!empty($queryDate) && count($queryDate) === 2) {
        //     $startDate = Carbon::parse($queryDate[0])->startOfDay();
        //     $endDate = Carbon::parse($queryDate[1])->endOfDay();
        //     $salesQuery->whereBetween('created_at', [$startDate, $endDate]);
        // }

        // // Filtrar por cliente si se proporciona
        // if (!empty($queryClient)) {
        //     $salesQuery->where('client_id', $queryClient);
        // }

        // // Realizar la consulta y devolver los resultados
        // $sales = SaleResource::collection($salesQuery->take(20)->get());

        return response()->json(['items' => $groupedSales]);
    }


    public function getItemsByPage($currentPage)
    {
        $offset = 5 + $currentPage * 5; //multiplica por 2 para traer de 2 dias en 2 dias. suma 5 dias porque son los que ya se cargaron
        $skip_days = $currentPage * 5; //multiplica por 2 para traer de 2 dias en 2 dias

         // Calcular la fecha hace x días para recuperar las ventas de x dias atras hasta la fecha de hoy
         $days_ago = Carbon::now()->subDays($offset);
         // ignorar esa cantidad de dias porque ya se cargaron.
         $days_befor = Carbon::now()->subDays($skip_days);

         // Obtener las ventas registradas en los últimos 7 días
         $sales = Sale::whereDate('created_at', '>=', $days_ago)->whereDate('created_at', '<=', $days_befor)->latest()->get();
 
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
}
