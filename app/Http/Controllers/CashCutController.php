<?php

namespace App\Http\Controllers;

use App\Models\CashCut;
use App\Models\CashRegister;
use App\Models\Sale;
use Illuminate\Http\Request;

class CashCutController extends Controller
{
    
    public function index()
    {
        // obtiene la primera caja registradora de la tienda
        $cash_register = CashRegister::where('store_id', auth()->user()->store_id)->first();
        $cash_cuts = CashCut::where('cash_register_id', $cash_register->id)->latest()->get();

        // return $cash_cuts;
        return inertia('CashCut/Index', compact('cash_cuts'));
    }

    
    public function create()
    {
        //
    }

    
    public function store(Request $request)
    {
        // obtiene la primera caja registradora de la tienda
        $cash_register = CashRegister::with(['movements'])->where('store_id', auth()->user()->store_id)->first();

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
        $cash_register->started_cash = $request->counted_cash - $request->amount_withdrawn;
        $cash_register->current_cash = $request->counted_cash - $request->amount_withdrawn;
        $cash_register->save();
    }

    
    public function show($cash_cut_id)
    {
        $cash_cut = CashCut::with(['cashRegister.movements'])->find($cash_cut_id);

        // return $cash_cut;
        return inertia('CashCut/Show', compact('cash_cut'));
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


    public function fetchTotalSaleForCashCut()
    {
        // obtiene la primera caja registradora de la tienda
        $cash_register = CashRegister::where('store_id', auth()->user()->store_id)->first();

        //recupera el último corte realizado
        $last_cash_cut = CashCut::where('cash_register_id', $cash_register->id)->latest()->first();

         // Si existe el último corte, recupera todas las ventas desde la fecha del último corte hasta ahora
        if ($last_cash_cut !== null) {
            $sales = Sale::where('store_id', auth()->user()->store_id)
                        ->where('created_at', '>', $last_cash_cut->created_at)
                        ->get();
        } else {
            $sales = Sale::where('store_id', auth()->user()->store_id)->get();
        }

        // Calcula el total de ventas
        $total_sales = $sales->sum(function ($sale) {
            return $sale->quantity * $sale->current_price;
        });

        return $total_sales;
    }
}
