<?php

namespace App\Http\Controllers;

use App\Models\DiscountTicket;
use App\Models\Partner;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    public function index()
    {
        //
    }

    public function create()
    {
        //
    }


    public function landingCreate()
    {
        return inertia('PartnerRegister');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:partners',
            'phone' => 'required|string|max:10|unique:partners',
            'code' => 'nullable|string|max:10',
        ], [
            'email.unique' => 'El correo ya se encuentra registrado',
            'phone.unique' => 'El teléfono ya se encuentra registrado',
        ]);

        // crear cupón y despues relacionarlo a partner
        $cupon = DiscountTicket::create([
            'code' => $request->code,
            'discount_amount' => 10,
            'description' => "Descuento de bienvenida para referidos de " . $request->name . ' ' . $request->last_name,
        ]);

        Partner::create([
            'name' => $request->name . ' ' . $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'discount_ticket_id' => $cupon->id,
        ]);
    }

    public function show(Partner $partner)
    {
        //
    }

    public function edit(Partner $partner)
    {
        //
    }

    public function update(Request $request, Partner $partner)
    {
        //
    }

    public function destroy(Partner $partner)
    {
        //
    }

    public function landingRecover(Request $request)
    {
        // buscar si existe algun partner con el correo dado
        $partner = Partner::where('email', $request->email)->first();

        if ($partner) {
            return response()->json(['code' => $partner->discountTicket->code, 'message' => null]);
        } else {
            return response()->json(['code' => null, 'message' => 'No hay ningún registro con ese correo']);
        }
    }
}
