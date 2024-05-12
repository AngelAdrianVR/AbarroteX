<?php

namespace App\Http\Controllers;

use App\Models\CashCut;
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
        // obtiene la caja registradora de la tienda
        $cash_register = CashRegister::find($request->cash_register_id);

        $request->validate([
            'cashRegisterMovementType' => 'required|string',
            //En caso de retirar dinero de la caja el monto máximo es lo que se tenga registrado en current_cash, es decir lo que hay en caja
            'registerAmount' => $request->cashRegisterMovementType == 'Ingreso'
                ? 'required|numeric|min:0|max:10000'
                : 'required|numeric|min:0|max:' . $cash_register->current_cash,
            'registerNotes' => 'nullable|string|max:255',
        ]);

        // Crea el movimiento de la caja obtenida anteriormente.
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


    public function fetchTotalCashMovements($cash_register_id)
    {
        //recupera el último corte realizado
        $last_cash_cut = CashCut::where('cash_register_id', $cash_register_id)->latest()->first();

         // Si existe el último corte, recupera todas las ventas desde la fecha del último corte hasta ahora
        if ($last_cash_cut !== null) {
            $movements = CashRegisterMovement::where('cash_register_id', $cash_register_id)
                        ->where('created_at', '>', $last_cash_cut->created_at)
                        ->get();
        } else {
            $movements = CashRegisterMovement::where('cash_register_id', $cash_register_id)->get();
        }

        // Calcula el total de movimientos
        $total_cash_movements = 0;

        foreach ($movements as $movement) {
            if ($movement->type === 'Ingreso') {
                $total_cash_movements += $movement->amount;
            } else if ($movement->type === 'Retiro') {
                $total_cash_movements -= $movement->amount;
            }
        }

        return $total_cash_movements;
    }


    public function fetchCurrentMovements($cash_register_id)
    {
        //----------- Recuperar los movimientos de caja desde el ultimo corte hasta ahora.------------
        //recupera el último corte realizado
        $last_cash_cut = CashCut::where('cash_register_id', $cash_register_id)->latest()->first();

        // Si existe el último corte, recupera todas las ventas desde la fecha del último corte hasta ahora
        if ($last_cash_cut !== null) {
            $current_movements = CashRegisterMovement::where('cash_register_id', $cash_register_id)
                        ->where('created_at', '>', $last_cash_cut->created_at)
                        ->get();
        } else {
            $current_movements = CashRegisterMovement::where('cash_register_id', $cash_register_id)->get();
        }

        return response()->json(['items' => $current_movements]);
    }
}
