<?php

namespace App\Http\Controllers;

use App\Models\CashCut;
use App\Models\CashRegister;
use App\Models\CashRegisterMovement;
use App\Models\CreditSaleData;
use App\Models\OnlineSale;
use App\Models\Sale;
use App\Models\Store;
use Carbon\Carbon;
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
            + $request->totalStoreSale['cash']
            // + $request->totalStoreSale['card'] // las ventas con tarjeta no se suman al corte de caja, ya que no se cuentan en efectivo.
            + $request->totalOnlineSale;

        //Crea el registro de corte de caja
        CashCut::create([
            'started_cash' => $cash_register->started_cash,
            'expected_cash' => $expected_cash, //suma de ventas, ingresos, retiros de caja y dinero inicial.
            'store_sales_cash' => $request->totalStoreSale['cash'], //ventas de tienda en efectivo
            'store_sales_card' => $request->totalStoreSale['card'], //ventas de tienda con tarjeta
            'online_sales_cash' => $request->totalOnlineSale, //ventas en línea
            'counted_cash' => $request->counted_cash, //dinero contado manualmente
            'difference' => $request->difference * -1, //se multiplica por menos 1 para guardar en la base de datos negativo si la diferencia fue negativa (faltó dinero)
            'withdrawn_cash' => $request->withdrawn_cash, 
            'notes' => $request->notes,
            'cash_register_id' => $cash_register->id,
            'store_id' => auth()->user()->store_id,
            'user_id' => auth()->id(),
        ]);

        //se asigna el dinero contado al dinero inicial de caja registradora para el próximo corte 
        //el dinero contado menos el dinero retirado.
        $cash_register->started_cash = $request->counted_cash - $request->withdrawn_cash;
        $cash_register->current_cash = $request->counted_cash - $request->withdrawn_cash;
        $cash_register->save();
    }


    public function show($created_at)
    {
        // Parsear la fecha recibida para obtener solo la parte de la fecha
        $date = Carbon::parse($created_at)->toDateString();

        // Obtener los cortes registrados en la fecha recibida
        $cash_cuts = CashCut::with(['cashRegister:id,name', 'user:id,name'])->where('store_id', auth()->user()->store_id)->whereDate('created_at', $date)->latest()->get();

        // Agrupar los cortes por fecha con el nuevo formato de fecha y calcular el total de venta y la diferencia para cada fecha
        $groupedCashCuts = $cash_cuts->groupBy(function ($date) {
            return $date->created_at->format('Y-m-d');
        })
            ->map(function ($group) {
                $total_store_sales = $group->sum('store_sales_cash') + $group->sum('store_sales_card');
                $total_online_sales = $group->sum('online_sales_cash') + $group->sum('online_sales_card');
                $total_difference = $group->sum('difference');
                $amount_sales_products = $group->count();

                return [
                    'cuts' => $group,
                    'total_store_sales' => $total_store_sales,
                    'total_online_sales' => $total_online_sales,
                    'total_sales' => $total_store_sales + $total_online_sales,
                    'total_difference' => $total_difference,
                    'amount_sales_products' => $amount_sales_products
                ];
            });
        
        // return $groupedCashCuts;
        return inertia('CashRegister/Show', compact('groupedCashCuts'));
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

    //METODO PARA RECUPERAR VENTAS PARA CORTE SIN TOMAR EN CUENTA PAGOS CON TARJETA
    // public function fetchTotalSaleForCashCut($cash_register_id)
    // {
    //     //recupera el último corte realizado
    //     $last_cash_cut = CashCut::where('cash_register_id', $cash_register_id)->latest()->first();

    //     //recupera las configuraciones de la tienda en linea.
    //     $online_store_properties = auth()->user()->store->online_store_properties;
    //     $online_sales = null;

    //     // Si existe el último corte, recupera todas las ventas desde la fecha del último corte hasta ahora
    //     if ($last_cash_cut !== null) {
    //         $sales = Sale::where('cash_register_id', $cash_register_id)
    //             ->where('created_at', '>', $last_cash_cut->created_at)
    //             ->where('payment_method', 'Efectivo')
    //             ->get();
            
    //         //recupera las ventas en línea si la caja registradora configurada para esas ventas es la misma enviada por parametro.
    //         if ($online_store_properties && $online_store_properties['online_sales_cash_register'] === intval($cash_register_id)) {
    //             $online_sales = OnlineSale::where('store_id', auth()->user()->store_id)
    //                 ->whereIn('status', ['Entregado', 'Reembolsado'])
    //                 ->where('created_at', '>', $last_cash_cut->created_at)
    //                 ->get();
    //         }
    //     } else {
    //         //recupera las ventas en línea si la caja registradora configurada para esas ventas es la misma enviada por parametro.
    //         if ($online_store_properties && $online_store_properties['online_sales_cash_register'] === intval($cash_register_id)) {

    //             $online_sales = OnlineSale::where('store_id', auth()->user()->store_id)
    //                 ->whereIn('status', ['Entregado', 'Reembolsado'])
    //                 ->get();
    //         }
    //         $sales = Sale::where('cash_register_id', $cash_register_id)->where('payment_method', 'Efectivo')->get();
    //     }

    //     // Filtra las ventas a crédito por folio
    //     $credit_sales_folios = CreditSaleData::pluck('folio')->toArray();
    //     $filtered_sales = $sales->reject(function ($sale) use ($credit_sales_folios) {
    //         return in_array($sale->folio, $credit_sales_folios);
    //     });

    //     // Calcula el total de ventas
    //     $total_sales = $filtered_sales->sum(function ($sale) {
    //         return $sale->quantity * $sale->current_price;
    //     });

    //     // Suma todos los totales de las ventas en línea más el costo de envío en caso de haber
    //     $total_online_sales = $online_sales?->sum(function ($online_sale) {
    //         return $online_sale->total + $online_sale->delivery_price;
    //     });
        

    //     return response()->json([
    //         'store_sales' => $total_sales ?? 0,
    //         'online_sales' => $total_online_sales ?? 0,
    //     ]);
    // }

    public function fetchTotalSaleForCashCut($cash_register_id)
    {
        $last_cash_cut = CashCut::where('cash_register_id', $cash_register_id)->latest()->first();
        $online_store_properties = auth()->user()->store->online_store_properties;
        $online_sales = null;

        if ($last_cash_cut !== null) {
            $sales = Sale::where('cash_register_id', $cash_register_id)
                ->where('created_at', '>', $last_cash_cut->created_at)
                ->get();

            if ($online_store_properties && $online_store_properties['online_sales_cash_register'] === intval($cash_register_id)) {
                $online_sales = OnlineSale::where('store_id', auth()->user()->store_id)
                    ->whereIn('status', ['Entregado', 'Reembolsado'])
                    ->where('created_at', '>', $last_cash_cut->created_at)
                    ->get();
            }
        } else {
            $sales = Sale::where('cash_register_id', $cash_register_id)->get();

            if ($online_store_properties && $online_store_properties['online_sales_cash_register'] === intval($cash_register_id)) {
                $online_sales = OnlineSale::where('store_id', auth()->user()->store_id)
                    ->whereIn('status', ['Entregado', 'Reembolsado'])
                    ->get();
            }
        }

        // Filtra las ventas a crédito
        $credit_sales_folios = CreditSaleData::pluck('folio')->toArray();
        $filtered_sales = $sales->reject(function ($sale) use ($credit_sales_folios) {
            return in_array($sale->folio, $credit_sales_folios);
        });

        // Separa ventas por método de pago
        $cash_sales = $filtered_sales->where('payment_method', 'Efectivo');
        $card_sales = $filtered_sales->where('payment_method', 'Tarjeta');

        // Calcula los totales
        $total_cash_sales = $cash_sales->sum(function ($sale) {
            return $sale->quantity * $sale->current_price;
        });

        $total_card_sales = $card_sales->sum(function ($sale) {
            return $sale->quantity * $sale->current_price;
        });

        $total_online_sales = $online_sales?->sum(function ($online_sale) {
            return $online_sale->total + $online_sale->delivery_price;
        });

        return response()->json([
            'store_sales' => [
                'cash' => $total_cash_sales,
                'card' => $total_card_sales,
            ],
            'online_sales' => $total_online_sales ?? 0,
        ]);
    }

    public function filterCashCuts(Request $request)
    {
        $queryDate = $request->input('queryDate');
        $startDate = Carbon::parse($queryDate[0])->startOfDay();
        $endDate = Carbon::parse($queryDate[1])->endOfDay();

        // Obtener los cortes registrados en el rango de fechas requerido por el filtro
        $cash_cuts = CashCut::where('store_id', auth()->user()->store_id)->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate)->latest()->get();

        // Agrupar los cortes por fecha con el nuevo formato de fecha y calcular el total de venta y la diferencia para cada fecha
        $groupedCashCuts = $cash_cuts->groupBy(function ($date) {
            return $date->created_at->format('Y-m-d');
        })
            ->map(function ($group) {
                $total_sales = $group->sum('sales_cash');
                $total_difference = $group->sum('difference');

                return [
                    'cuts' => $group,
                    'total_sales' => $total_sales,
                    'total_difference' => $total_difference
                ];
            });

        return response()->json(['items' => $groupedCashCuts]);
    }


    public function getItemsByPage($currentPage)
    {
        $offset = $currentPage * 7;

        $cash_cuts = CashCut::where('store_id', auth()->user()->store_id)->latest()->get();

        // Agrupar los cortes por fecha con el nuevo formato de fecha y calcular el total de venta y la diferencia para cada fecha
        $groupedCashCuts = $cash_cuts->groupBy(function ($date) {
            return $date->created_at->format('Y-m-d');
        })
            ->map(function ($group) {
                $total_sales = $group->sum('sales_cash');
                $total_difference = $group->sum('difference');

                return [
                    'cuts' => $group,
                    'total_sales' => $total_sales,
                    'total_difference' => $total_difference
                ];
            })->skip($offset)
            ->take(7);

        return response()->json(['items' => $groupedCashCuts]);
    }


    //--- Este método es para mostrar los movimientos relacionados a cada corte en el show de cortes ----
    public function getCashCutMovements(CashCut $cash_cut)
    {
        // Obtiene la caja registradora que realizó el corte
        $cash_register = CashRegister::find($cash_cut->cash_register_id);

        // Obtiene los cortes anteriores al corte actual en la misma caja
        $previous_cuts = CashCut::where('cash_register_id', $cash_register->id)
            ->where('created_at', '<', $cash_cut->created_at)
            ->orderBy('created_at', 'desc')
            ->get();

        // Obtiene los cortes posteriores al corte actual en la misma caja (para definir el rango superior si existe)
        $next_cuts = CashCut::where('cash_register_id', $cash_register->id)
            ->where('created_at', '>', $cash_cut->created_at)
            ->orderBy('created_at', 'asc')
            ->get();

        $startDate = null;
        $endDate = $cash_cut->created_at;

        // Si hay cortes anteriores, el último se convierte en el punto de referencia
        if ($previous_cuts->isNotEmpty()) {
            $reference_cut = $previous_cuts->first();
            $startDate = $reference_cut->created_at;
        }

        // Construye la consulta para los movimientos de caja
        $query = CashRegisterMovement::where('cash_register_id', $cash_register->id);

        if ($startDate) {
            $query->where('created_at', '>', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<', $endDate);
        } else {
            $query->where('created_at', $cash_cut->created_at);
        }

        // Obtiene los movimientos de caja dentro del rango definido
        $cash_cut_movements = $query->get();

        return response()->json(['items' => $cash_cut_movements]);
    }
}
