<?php

namespace App\Http\Controllers;

use App\Models\CashCut;
use App\Models\CashRegister;
use App\Models\CashRegisterMovement;
use App\Models\User;
use Illuminate\Http\Request;

class CashRegisterController extends Controller
{
    
    public function index()
    {   
        // obtiene las cajas registradoras de la tienda
        $cash_registers = CashRegister::where('store_id', auth()->user()->store_id)->get();

        //obtiene los cortes de todas las cajas de la tienda
        $cash_cuts = CashCut::where('cash_register_id', $cash_registers[0]->id)->latest()->get();
        
        return inertia('CashRegister/Index', compact('cash_registers', 'cash_cuts'));
    }

   
    public function create()
    {
        return inertia('CashRegister/Create');
    }

    
    public function store(Request $request)
    {
        CashRegister::create([
            'name' => $request->name ?? 'Nueva caja',
            'started_cash' => $request->started_cash ?? 0,
            'current_cash' => $request->started_cash ?? 0,
            'max_cash' => $request->max_cash ?? 5000,
            'store_id' => auth()->user()->store_id,
        ]);

        return to_route('cash-registers.index');
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
            'name' => 'required|string|max:100',
            'is_active' => 'boolean',
        ]);

        $cash_register->update($validated);
    }

    
    public function destroy(CashRegister $cash_register)
    {
        //
    }


    public function fetchCashRegister($cash_register_id)
    {
        //recupera la primera caja registradora de la tienda para mandar su info como current_cash
        $cash_register = CashRegister::find($cash_register_id);

        return response()->json(['item' => $cash_register]);
    }


    public function asignCashRegister(User $user, $cash_register_id)
    {
        $user->update([
            'cash_register_id' => $cash_register_id
        ]);
    }
}
