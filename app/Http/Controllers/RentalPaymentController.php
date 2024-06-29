<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use App\Models\RentalPayment;
use Illuminate\Http\Request;

class RentalPaymentController extends Controller
{

    public function index()
    {
        //
    }

    public function create()
    {
        $rental_id = intval(request('rentalId'));
        $rentals = Rental::where('store_id', auth()->user()->store_id)
            ->get()
            ->map(fn ($rent) => ['id' => $rent->id, 'folio' => "R-$rent->folio: {$rent->client->name}"]);

        return inertia('RentalPayment/Create', compact('rental_id', 'rentals'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'paid_date' => 'required|date',
            'amount' => 'required|numeric|min:0|max:999999.99',
            'concept' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:255',
            'rental_id' => 'required|numeric|min:1',
        ]);

        RentalPayment::create($validated + ['user_id' => auth()->id()]);

        return to_route('rentals.show', base64_encode($validated['rental_id']));
    }

    public function show(RentalPayment $rentalPayment)
    {
        //
    }

    public function edit(RentalPayment $rentalPayment)
    {
        //
    }

    public function update(Request $request, RentalPayment $rentalPayment)
    {
        //
    }

    public function destroy(RentalPayment $rentalPayment)
    {
        //
    }
}
