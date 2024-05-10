<?php

namespace App\Http\Controllers;

use App\Models\CashCut;
use App\Models\CashRegister;
use App\Models\CashRegisterMovement;
use Illuminate\Http\Request;

class CashRegisterController extends Controller
{
    
    public function index()
    {   
        // obtiene las cajas registradoras de la tienda
        $cash_registers = CashRegister::where('store_id', auth()->user()->store_id)->get();
        $cash_cuts = CashCut::where('cash_register_id', $cash_registers[0]->id)->latest()->get();

        //----------- Recuperar los movimientos de caja desde el ultimo corte hasta ahora.------------
         //recupera el último corte realizado
         $last_cash_cut = CashCut::where('cash_register_id', $cash_registers[0]->id)->latest()->first();

         // Si existe el último corte, recupera todas las ventas desde la fecha del último corte hasta ahora
        if ($last_cash_cut !== null) {
            $current_movements = CashRegisterMovement::where('cash_register_id', $cash_registers[0]->id)
                        ->where('created_at', '>', $last_cash_cut->created_at)
                        ->get();
        } else {
            $current_movements = CashRegisterMovement::where('cash_register_id', $cash_registers[0]->id)->get();
        }
        
        return inertia('CashRegister/Index', compact('cash_cuts', 'cash_registers', 'current_movements'));
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
