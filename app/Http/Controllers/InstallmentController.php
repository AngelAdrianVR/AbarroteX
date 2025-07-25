<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\CashRegisterMovement;
use App\Models\CreditSaleData;
use App\Models\Installment;
use App\Models\Sale;
use Illuminate\Http\Request;

class InstallmentController extends Controller
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
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'credit_sale_data_id' => 'required|numeric|min:1',
        ]);


        // actualizar status de la venta a credito
        $credit_sale_data = CreditSaleData::findOrFail($validated['credit_sale_data_id']);
        $installment = Installment::create($validated + ['user_id' => auth()->id(), 'client_id' => $credit_sale_data->client_id]);

        // Obtener todas las ventas asociadas a la venta a crédito
        $sales = Sale::where([
            'folio' => $credit_sale_data->folio,
            'store_id' => auth()->user()->store_id,
        ])->get();

        CashRegisterMovement::create([
            'amount' => $validated['amount'],
            'type' => 'Ingreso',
            'notes' => "Abono registrado de la venta con folio $credit_sale_data->folio",
            'cash_register_id' => $sales->first()->cash_register_id,
        ]);

        // Calcular el monto total de la venta
        $totalSaleAmount = $sales->sum(function ($sale) {
            $price_to_use = $sale->discounted_price !== null
                ? $sale->discounted_price
                : $sale->current_price;

            return $sale->quantity * $price_to_use;
        });

        // Calcular el monto total de todos los abonos realizados
        $totalInstallmentsAmount = $credit_sale_data->installments->sum('amount');

        // obtener cualquier producto para actualizar a cliente
        $first_sale = Sale::firstWhere([
            'folio' => $credit_sale_data->folio,
            'store_id' => auth()->user()->store_id,
        ]);
        $first_sale->client->debt -= $installment->amount;
        $first_sale->client->save();

        // Actualizar el estado de la venta a crédito
        if ($totalInstallmentsAmount >= $totalSaleAmount) {
            $credit_sale_data->status = 'Pagado';
        } else {
            $credit_sale_data->status = 'Parcial';
        }
        // Guardar los cambios
        $credit_sale_data->save();

        // sumar a caja el monto abonado
        $cash_register = CashRegister::find(auth()->user()->cash_register_id);
        $cash_register->current_cash += $installment->amount;
        $cash_register->save();
    }

    public function show(Installment $installment)
    {
        //
    }

    public function edit(Installment $installment)
    {
        //
    }

    public function update(Request $request, Installment $installment)
    {
        //
    }

    public function destroy(Installment $installment)
    {
        //
    }
}
