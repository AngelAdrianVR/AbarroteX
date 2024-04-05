<?php

namespace App\Http\Controllers;

use App\Http\Resources\SaleResource;
use App\Models\Product;
use App\Models\ProductHistory;
use App\Models\Sale;
use App\Notifications\BasicNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function pointIndex()
    {
        $products = Product::all(['id', 'name', 'code']);

        // return $products;
        return inertia('Sale/Point', compact('products'));
    }

    
    public function index()
    {
        // Obtener las ventas más recientes
        $sales = Sale::latest()->take(20)->get();

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

        $total_sales = Sale::all()->count();

        // return $groupedSales;
        return inertia('Sale/Index', compact('groupedSales', 'total_sales'));
    }

    
    public function create()
    {
        //
    }

    
    public function store(Request $request)
    {
        foreach ($request->data['saleProducts'] as $sale) {
            //regiatra cada producto vendido
            Sale::create([
                'current_price' => $sale['product']['public_price'],
                'quantity' => $sale['quantity'],
                'product_id' => $sale['product']['id'],
            ]);

            //Registra el historial de venta de cada producto
            ProductHistory::create([
                'description' => 'Registro de venta. ' . $sale['quantity'] . ' piezas',
                'type' => 'Venta',
                'product_id' => $sale['product']['id']
            ]);

            //rebaja del stock la cantidad de piezas vendidas
            $product = Product::find($sale['product']['id']);
            $product->decrement('current_stock', $sale['quantity']);

            // notificar si ha llegado al limite de existencias bajas
            if ($product->current_stock <= $product->min_stock) {
                $title = "Bajo stock";
                $description = "Producto <span class='text-primary'>$product->name</span> alcanzó el nivel mínimo establecido";
                $url = route('products.show', $product->id);

                auth()->user()->notify(new BasicNotification($title, $description, $url));
            }

        }
    }

    
    public function show(Sale $sale)
    {
        // $sale = SaleResource::make(Sale::with('client', 'payments', 'products')->find($sale_id));
        // $clients = Client::all(['id', 'name']);

        // // return $sale;
        // return inertia('Sale/Show', compact('sale', 'clients'));
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
        $sale->delete();
    }


    // public function searchProduct(Request $request)
    // {
    //     $queryDate = $request->input('queryDate');

    //     $salesQuery = Sale::with('client', 'payments', 'products');

    //     // Filtrar por rango de fechas si se proporciona
    //     if (!empty($queryDate) && count($queryDate) === 2) {
    //         $startDate = Carbon::parse($queryDate[0])->startOfDay();
    //         $endDate = Carbon::parse($queryDate[1])->endOfDay();
    //         $salesQuery->whereBetween('created_at', [$startDate, $endDate]);
    //     }

    //     // Filtrar por cliente si se proporciona
    //     if (!empty($queryClient)) {
    //         $salesQuery->where('client_id', $queryClient);
    //     }

    //     // Realizar la consulta y devolver los resultados
    //     $sales = SaleResource::collection($salesQuery->take(20)->get());

    //     return response()->json(['items' => $sales]);
    // }


    public function getItemsByPage($currentPage)
    {
        $offset = $currentPage * 20;
        $sales = SaleResource::collection(Sale::latest()
            ->skip($offset)
            ->take(20)
            ->get());

        return response()->json(['items' => $sales]);
    }
}
