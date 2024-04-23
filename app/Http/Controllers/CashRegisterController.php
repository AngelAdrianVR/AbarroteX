<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\CashRegisterMovement;
use Illuminate\Http\Request;

class CashRegisterController extends Controller
{
    
    public function index()
    {   
        // obtiene la primera caja registradora de la tienda
        $cash_register = CashRegister::where('store_id', auth()->user()->store_id)->first();
        $cash_register_movements = CashRegisterMovement::where('cash_register_id', $cash_register->id)->latest()->get();

        // return $cash_register_movements;
        return inertia('CashRegister/Index', compact('cash_register_movements'));
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
        //
    }

    
    public function destroy(CashRegister $cash_register)
    {
        //
    }


    public function fetchCurrentCash(CashRegister $cash_register)
    {
        //recupera la primera caja registradora de la tienda para mandar su info como current_cash
        $cash_register = CashRegister::where('store_id', auth()->user()->store_id)->first();

        return response()->json(['item' => $cash_register->current_cash]);
    }
}
