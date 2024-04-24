<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\CashRegisterMovement;
use Illuminate\Http\Request;

class CashRegisterMovementController extends Controller
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
        // obtiene la primera caja registradora de la tienda
        $cash_register = CashRegister::where('store_id', auth()->user()->store_id)->first();

        $request->validate([
            'cashRegisterMovementType' => 'required|string',
            //En caso de retirar dinero de la caja el monto máximo es lo que se tenga registrado en current_cash, es decir lo que hay en caja
            'registerAmount' => $request->cashRegisterMovementType === 'Ingreso'
                ? 'required|numeric|min:0|max:10000'
                : 'required|numeric|min:0|max:' . $cash_register->current_cash,
            'registerNotes' => 'nullable|string|max:255',
        ]);

        // Crea el movimiento de la caja obtenida anteriormente. En caso de haber varias cajas ajustar lógica
        CashRegisterMovement::create([
            'amount' => $request->registerAmount,
            'type' => $request->cashRegisterMovementType,
            'notes' => $request->registerNotes,
            'cash_register_id' => $cash_register->id,
        ]);

        //En caso de ser ingreso, suma la cantidad al dinero actual de la caja, en caso contrario lo resta
        if ($request->cashRegisterMovementType === 'Ingreso') {
            $cash_register->current_cash += $request->registerAmount;
        } else {
            $cash_register->current_cash -= $request->registerAmount;
        }

        //Guarda la cantidad modificada
        $cash_register->save();
    }


    public function show(CashRegisterMovement $cash_register_movement)
    {
        //
    }


    public function edit(CashRegisterMovement $cash_register_movement)
    {
        //
    }


    public function update(Request $request, CashRegisterMovement $cash_register_movement)
    {
        //
    }


    public function destroy(CashRegisterMovement $cash_register_movement)
    {
        //
    }
}
