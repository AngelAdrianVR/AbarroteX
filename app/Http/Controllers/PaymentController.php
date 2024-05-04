<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
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
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0|max:999999.99',
            'suscription_period' => 'required|string|max:255',
            'days_gifted' => 'required|numeric|min:0',
        ]);

        $store = auth()->user()->store;

        $additional = [
            'payment_method' => 'Transferencia / Depósito',
            'store_id' => $store->id,
        ];

        $payment = Payment::create($validated + $additional);

        // Guardar archivos adjuntos
        $payment->addAllMediaFromRequest()->each(fn ($file) => $file->toMediaCollection());

        // dias adquiridos
        $days = $request->days + $validated['days_gifted'];

        // Actualizar datos de suscripcion en tienda
        $store->update([
            'suscription_period' => $validated['suscription_period'],
            'next_payment' => $store->next_payment->addDays($days)->toDateString(),
        ]);
    }


    public function show(Payment $payment)
    {
        //
    }


    public function edit(Payment $payment)
    {
        //
    }


    public function update(Request $request, Payment $payment)
    {
        //
    }


    public function destroy(Payment $payment)
    {
        //
    }
}
