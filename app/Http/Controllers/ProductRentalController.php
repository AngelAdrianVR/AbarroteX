<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Product;
use App\Models\ProductRental;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProductRentalController extends Controller
{

    public function index()
    {
        $total_rentals = ProductRental::with(['client', 'product'])
            ->where('store_id', auth()->user()->store_id)
            ->latest()
            ->get();
        $total_product_rentals = $total_rentals->count();
        $product_rentals = $total_rentals->take(30);

        return inertia('ProductRental/Index', compact('product_rentals', 'total_product_rentals'));
    }

    public function create()
    {
        $clients = Client::where('store_id', auth()->user()->store_id)->get(['id', 'name']);
        $products = Product::where('store_id', auth()->user()->store_id)->get(['id', 'name']);

        return inertia('ProductRental/Create', compact('clients', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'period' => 'required',
            'cost' => 'required|numeric|min:0',
            'status' => 'required|string|max:255',
            'product_id' => 'required|numeric|min:1',
            'client_id' => 'required|numeric|min:1',
            'notes' => 'nullable|string|max:400',
            'rented_date' => 'required|date',
            'rented_time' => 'required|string',
            'estimated_end_date' => 'nullable|date',
            'estimated_end_time' => 'nullable|string',
        ]);

        // actualizar timesptamp dependiendo del status al crear la renta
        if ($validated['status'] == 'Cancelado') {
            $validated['cancelled_at'] = now()->toDateTimeString();
        } elseif ($validated['status'] == 'Completado') {
            $validated['completed_at'] = now()->toDateTimeString();
        }

        $validated['rented_at'] = Carbon::parse($validated['rented_date'])->toDateString() . ' ' . $validated['rented_time'];

        $rent = ProductRental::create($validated + ['store_id' => auth()->user()->store_id]);

        return to_route('product-rentals.show', $rent);
    }


    public function show($encoded_product_id)
    {
        // Decodificar el ID
        $decoded_product_id = base64_decode($encoded_product_id);
        $product_rental = ProductRental::with(['client', 'product'])->find($decoded_product_id);

        return inertia('ProductRental/Show', compact('product_rental'));
    }


    public function edit(ProductRental $productRental)
    {
        //
    }


    public function update(Request $request, ProductRental $productRental)
    {
        //
    }


    public function destroy(ProductRental $productRental)
    {
        $productRental->delete();
    }

    // API
    public function updateStatus(Request $request, ProductRental $product_rental)
    {
        $product_rental->update([
            'status' => $request->status,
            'cancelled_at' => $request->status == 'Cancelado' ? now() : null,
            'completed_at' => $request->status == 'Completado' ? now() : null,
        ]);

        return response()->json(['item' => $product_rental->load(['client', 'product'])]);
    }

    public function searchRent(Request $request)
    {
        $query = $request->input('query');

        // Realiza la búsqueda en la base de datos local
        $rentals = ProductRental::with(['client', 'product'])
            ->where('store_id', auth()->user()->store_id)
            ->where(function ($q) use ($query) {
                $q->whereHas('client', function ($client) use ($query) {
                    $client->where('name', 'like', "%$query%");
                })
                    ->orWhereHas('product', function ($product) use ($query) {
                        $product->where('name', 'like', "%$query%");
                    });
            })
            ->get();

        return response()->json(['items' => $rentals]);
    }

    public function getItemsByPage($currentPage)
    {
        $offset = $currentPage * 30;

        // obtener todo los productos
        $total_rentals = ProductRental::with(['client', 'product'])
            ->where('store_id', auth()->user()->store_id)
            ->latest()
            ->get();

        $rentals = $total_rentals->splice($offset)->take(30);

        return response()->json(['items' => $rentals]);
    }
}
