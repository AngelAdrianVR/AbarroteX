<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Product;
use App\Models\Rental;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RentalController extends Controller
{

    public function index()
    {
        $total_rentals = Rental::with(['client', 'product'])
            ->where('store_id', auth()->user()->store_id)
            ->latest()
            ->get();
        $total_product_rentals = $total_rentals->count();
        $rentals = $total_rentals->take(30);

        return inertia('Rental/Index', compact('rentals', 'total_product_rentals'));
    }

    public function create()
    {
        $clients = Client::where('store_id', auth()->user()->store_id)->get(['id', 'name']);
        $products = Product::where('store_id', auth()->user()->store_id)->get(['id', 'name']);

        return inertia('Rental/Create', compact('clients', 'products'));
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

        // Generar un id unico para rentas de esta tienda o negocio
        $last_rental = Rental::where('store_id', auth()->user()->store_id)->latest('id')->first();
        $folio = $last_rental ? intval($last_rental->folio) + 1 : 1;

        // actualizar timesptamp dependiendo del status al crear la renta
        if ($validated['status'] == 'Cancelado') {
            $validated['cancelled_at'] = now()->toDateTimeString();
        } elseif ($validated['status'] == 'Completado') {
            $validated['completed_at'] = now()->toDateTimeString();
        }

        $validated['rented_at'] = Carbon::parse($validated['rented_date'])->toDateString() . ' ' . $validated['rented_time'];

        $rent = Rental::create($validated + ['store_id' => auth()->user()->store_id, 'user_id' => auth()->id(), 'folio' => $folio]);

        return to_route('rentals.show', base64_encode($rent->id));
    }


    public function show($encoded_rental_id)
    {
        // Decodificar el ID
        $decoded_rental_id = base64_decode($encoded_rental_id);
        $rental = Rental::with(['client', 'product', 'payments'])->find($decoded_rental_id);
        $rentals = Rental::where('store_id', auth()->user()->store_id)
            ->get()
            ->map(fn ($rent) => ['id' => $rent->id, 'folio' => "R-$rent->folio: {$rent->client->name}"]);

        return inertia('Rental/Show', compact('rental', 'rentals'));
    }


    public function edit($encoded_rental_id)
    {
        // Decodificar el ID
        $decoded_rental_id = base64_decode($encoded_rental_id);
        $rental = Rental::with(['client', 'product'])->find($decoded_rental_id);
        $clients = Client::where('store_id', auth()->user()->store_id)->get(['id', 'name']);
        $products = Product::where('store_id', auth()->user()->store_id)->get(['id', 'name']);

        return inertia('Rental/Edit', compact('clients', 'products', 'rental'));
    }


    public function update(Request $request, Rental $rental)
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

        $rental->update($validated);

        return to_route('rentals.show', base64_encode($rental->id));
    }


    public function destroy(Rental $rental)
    {
        $rental->delete();
    }

    // API
    public function updateStatus(Request $request, Rental $rental)
    {
        $rental->update([
            'status' => $request->status,
            'cancelled_at' => $request->status == 'Cancelado' ? now() : null,
            'completed_at' => $request->status == 'Completado' ? now() : null,
        ]);

        return response()->json(['item' => $rental->load(['client', 'product'])]);
    }

    public function searchRent(Request $request)
    {
        $query = $request->input('query');

        // Realiza la búsqueda en la base de datos local
        $rentals = Rental::with(['client', 'product'])
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
        $total_rentals = Rental::with(['client', 'product'])
            ->where('store_id', auth()->user()->store_id)
            ->latest()
            ->get();

        $rentals = $total_rentals->splice($offset)->take(30);

        return response()->json(['items' => $rentals]);
    }
}
