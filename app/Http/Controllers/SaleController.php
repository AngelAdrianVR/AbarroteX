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

            //regiatra cada producto vendido
            Sale::create([
                'current_price' => $sale['product']['public_price'],
                'quantity' => $sale['quantity'],
                'product_id' => isset($sale['product']['global_product_id']) ? null : $sale['product']['id'], // en caso de vender un producto local
                'global_product_store_id' => isset($sale['product']['global_product_id']) ? $sale['product']['id'] : null, // en caso de vender un producto transferido del catálogo
                'store_id' => auth()->user()->store_id,
            ]);

            //Registra el historial de venta de cada producto
            ProductHistory::create([
                'description' => 'Registro de venta. ' . $sale['quantity'] . ' piezas',
                'type' => 'Venta',
                'product_id' => isset($sale['product']['global_product_id']) ? null : $sale['product']['id'], // en caso de vender un producto local
                'global_product_store_id' => isset($sale['product']['global_product_id']) ? $sale['product']['id'] : null, // en caso de vender un producto transferido del catálogo
            ]);

             // Verifica si 'global_product_id' existe en 'product'
             if (isset($sale['product']['global_product_id'])) {
                // Si existe, recupera el producto global de la tienda'
                $product = GlobalProductStore::find($sale['product']['id']);
                $product->decrement('current_stock', $sale['quantity']);
            } else {
                // Si no existe, asigna el valor de 'id' dentro de 'product'
                //rebaja del stock la cantidad de piezas vendidas
                $product = Product::find($sale['product']['id']);
                $product->decrement('current_stock', $sale['quantity']);
            }
            

            // // notificar si ha llegado al limite de existencias bajas
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
        $sales = Sale::where('store_id', auth()->user()->store_id)->with(['product:id,name', 'globalProductStore.globalProduct'])->whereDate('created_at', $date)->get();

        // return $sales;

        $localSales = collect();
        $globalSales = collect();

        // Agrupar las ventas por fecha con el nuevo formato de fecha y calcular el total de productos vendidos y el total de ventas para cada fecha
        $day_sales = $sales->groupBy(function ($sale) {
            return Carbon::parse($sale->created_at)->format('d-F-Y');
        })->map(function ($sales) use ($localSales, $globalSales) {
            // Iterar sobre las ventas y separarlas en ventas locales y globales
            $sales->each(function ($sale) use ($localSales, $globalSales) {
                if ($sale->global_product_store_id !== null) {
                    $globalSales->push($sale);
                } else {
                    $localSales->push($sale);
                }
            });

            // Procesar las ventas locales
            $localGroupedSales = $localSales->groupBy('product_id')->map(function ($productSales) {
                return [
                    'id' => $productSales->first()->id,
                    'name' => $productSales->first()->product->name,
                    'current_price' => $productSales->first()->current_price,
                    'quantity' => $productSales->sum('quantity'),
                    'product_id' => $productSales->first()->product_id,
                    'is_global_product' => false,
                    'created_at' => $productSales->first()->created_at,
                ];
            });

            // Procesar las ventas globales
            $globalGroupedSales = $globalSales->groupBy('global_product_store_id')->map(function ($productSales) {
                return [
                    'id' => $productSales->first()->id,
                    'name' => $productSales->first()->globalProductStore->globalProduct->name,
                    'current_price' => $productSales->first()->current_price,
                    'quantity' => $productSales->sum('quantity'),
                    'product_id' => $productSales->first()->global_product_store_id,
                    'is_global_product' => true,
                    'created_at' => $productSales->first()->created_at,
                ];
            });

            // Calcular totales para ventas locales
            $localTotalQuantity = $localGroupedSales->sum('quantity');
            $localTotalSale = $localGroupedSales->sum(function ($productSale) {
                return $productSale['quantity'] * $productSale['current_price'];
            });

            // Calcular totales para ventas globales
            $globalTotalQuantity = $globalGroupedSales->sum('quantity');
            $globalTotalSale = $globalGroupedSales->sum(function ($productSale) {
                return $productSale['quantity'] * $productSale['current_price'];
            });

            // Fusionar los arreglos de ventas locales y globales
            $mergedGroupedSales = $localGroupedSales->merge($globalGroupedSales);

            return [
                'total_quantity' => $localTotalQuantity + $globalTotalQuantity,
                'total_sale' => $localTotalSale + $globalTotalSale,
                'sales' => $mergedGroupedSales->values(), // Convertir el mapa en un arreglo indexado
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
}
