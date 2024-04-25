<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use Illuminate\Http\Request;

class CashRegisterController extends Controller
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
        //
    }

    
    public function show(CashRegister $cash_register)
    {
        //
    }

    
    public function edit(CashRegister $cash_register)
    {
        //
    }

    
    public function update(Request $request, CashRegister $cash_register)
    {
        $validated = $request->validate([
            'max_cash' => 'required|numeric|min:0|max:999999.99',
        ]);

        $cash_register->update($validated);
    }

    
    public function destroy(CashRegister $cash_register)
    {
        //
    }


    public function fetchCashRegister()
    {
        //recupera la primera caja registradora de la tienda para mandar su info como current_cash
        $cash_register = CashRegister::where('store_id', auth()->user()->store_id)->first();

        return response()->json(['item' => $cash_register]);
    }
}
