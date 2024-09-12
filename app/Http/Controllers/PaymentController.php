<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Payment;
use App\Notifications\AdminBasicNotification;
use Illuminate\Http\Request;

class PaymentController extends Controller
{

    public function index()
    {
        $payments = Payment::with('media')->where('store_id', auth()->user()->store_id)->get();

        return inertia('Payment/Index', compact('payments'));
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
        $payment->addAllMediaFromRequest()->each(fn($file) => $file->toMediaCollection());

        // dias adquiridos
        $days = $request->days + $validated['days_gifted'];

        // Actualizar datos de suscripcion en tienda
        $store->update([
            'suscription_period' => $validated['suscription_period'],
            'next_payment' => $store->next_payment->addDays($days)->toDateString(),
        ]);

        // Notificar a dirección
        $admins = Admin::where('employee_properties->department', 'Dirección')->get();
        $title = "Nuevo pago registrado";
        $description = "La tienda '$store->name' ha pagado una suscripción {$validated['suscription_period']} ($ {$validated['amount']}).";
        if (app()->environment() === 'production') {
            $url = 'https://admin.ezyventas.com/stores';
        } else {
            $url = 'http://localhost:8000/stores';
        }
        $admins->each(fn($admin) => $admin->notify(new AdminBasicNotification($title, $description, $url)));
    }

    public function storeInvoice(Request $request, Payment $payment)
    {
        // borrar constancia anterior si es que la hay
        if ($payment->getFirstMedia('invoice')) {
            $payment->clearMediaCollection('invoice');
        }

        $payment->addAllMediaFromRequest()->each(fn($file) => $file->toMediaCollection('invoice'));
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
        $payment->update($request->all());
    }


    public function destroy(Payment $payment)
    {
        //
    }
}
