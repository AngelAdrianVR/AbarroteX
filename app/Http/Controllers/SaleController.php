<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductHistory;
use App\Models\Sale;
use App\Notifications\BasicNotification;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    
    public function index()
    {
        $products = Product::all(['id', 'name', 'code']);

        // return $products;
        return inertia('Sale/Index', compact('products'));
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
        //
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
        //
    }
}
