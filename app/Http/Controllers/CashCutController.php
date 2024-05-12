<?php

namespace App\Http\Controllers;

use App\Models\CashCut;
use App\Models\CashRegister;
use App\Models\CashRegisterMovement;
use App\Models\Sale;
use Illuminate\Http\Request;

class CashCutController extends Controller
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
        $request->validate([
            'withdrawn_cash' => 'nullable|numeric|min:0|max:' . $request->counted_cash, 
        ]);

        // obtiene la caja registradora de la tienda a la cual se hará el corte
        $cash_register = CashRegister::with(['movements'])->find($request->cash_register_id);

        //suma algebraica de todo el dinero que ingresó y salió de caja
        $expected_cash = $cash_register->started_cash
                            + $request->totalCashMovements 
                            + $request->totalSaleForCashCut;

        //Crea el registro de corte de caja
        CashCut::create([
            'started_cash' => $cash_register->started_cash,
            'expected_cash' => $expected_cash, //suma de ventas, ingresos, retiros de caja y dinero inicial.
            'sales_cash' => $request->totalSaleForCashCut,
            'counted_cash' => $request->counted_cash,
            'difference' => $request->difference * -1,
            'notes' => $request->notes,
            'cash_register_id' => $cash_register->id,
        ]);

        //se asigna el dinero contado al dinero inicial de caja registradora para el próximo corte 
        //el dinero contado menos el dinero retirado.
        $cash_register->started_cash = $request->counted_cash - $request->withdrawn_cash;
        $cash_register->current_cash = $request->counted_cash - $request->withdrawn_cash;
        $cash_register->save();
    }

    
    public function show(CashCut $cash_cut)
    {
        // obtiene la primera caja registradora de la tienda
        $cash_register = CashRegister::where('store_id', auth()->user()->store_id)->first();

        // Se obtiene el ultimo corte para guardar los movimientos de caja hechos desdes de este ultimo corte
        $last_cash_cut = CashCut::where('cash_register_id', $cash_register->id)->where('created_at', '<', $cash_cut->created_at)->latest()->first();

        if ( $last_cash_cut !== null) {
            //Se obtienen los movimientos de caja hechos despues del ultimo corte y antes de los cortes mas recientes
            $cash_cut_movements = CashRegisterMovement::where('cash_register_id', $cash_register->id)
                ->where('created_at', '>', $last_cash_cut->created_at )
                ->where('created_at', '<', $cash_cut->created_at)
                ->get();
        } else {
            // Se obtiene el segundo corte para limitar los movimientos del primer corte
            $second_cash_cut = CashCut::where('cash_register_id', $cash_register->id)
            ->skip(1)
            ->first();

            //Si existe este segundo corte entonces delimita los movimientos a esa fecha
            if ( $second_cash_cut !== null ) {
                $cash_cut_movements = CashRegisterMovement::where('cash_register_id', $cash_register->id)
                    ->where('created_at', '<', $second_cash_cut->created_at)
                    ->get();
            } else { //Si no existe el segundo corte obtiene todos los registrados
                $cash_cut_movements = CashRegisterMovement::where('cash_register_id', $cash_register->id)->get();
            }
        }
        
        // return $second_cash_cut;
        return inertia('CashCut/Show', compact('cash_cut', 'cash_cut_movements'));
    }

    
    public function edit(CashCut $cash_cut)
    {
        //
    }

    
    public function update(Request $request, CashCut $cash_cut)
    {
        //
    }

    
    public function destroy(CashCut $cash_cut)
    {
        $cash_cut->delete();
    }


    public function fetchTotalSaleForCashCut($cash_register_id)
    {
        //recupera el último corte realizado
        $last_cash_cut = CashCut::where('cash_register_id', $cash_register_id)->latest()->first();

         // Si existe el último corte, recupera todas las ventas desde la fecha del último corte hasta ahora
        if ($last_cash_cut !== null) {
            $sales = Sale::where('cash_register_id', $cash_register_id)
                        ->where('created_at', '>', $last_cash_cut->created_at)
                        ->get();
        } else {
            $sales = Sale::where('cash_register_id', $cash_register_id)->get();
        }

        // Calcula el total de ventas
        $total_sales = $sales->sum(function ($sale) {
            return $sale->quantity * $sale->current_price;
        });

        return $total_sales;
    }
}
