<?php

namespace App\Http\Controllers;

use App\Models\CashCut;
use App\Models\CashRegister;
use App\Models\User;
use App\Notifications\BasicEmailNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashRegisterController extends Controller
{

    public function index()
    {
        // obtiene las cajas registradoras de la tienda
        $cash_registers = CashRegister::where('store_id', auth()->user()->store_id)->get();

        // Obtener todos los cortes registrados y contar el número de agrupaciones por día
        $total_cash_cuts = DB::table('cash_cuts')
            ->select(DB::raw('DATE(created_at) as date'))
            ->where('store_id', auth()->user()->store_id)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get()
            ->count();

        //obtiene los cortes de todas las cajas de la tienda
        $cash_cuts = CashCut::where('store_id', auth()->user()->store_id)
            ->latest()
            ->get()
            ->groupBy(function ($date) {
                return $date->created_at->format('Y-m-d');
            })
            ->map(function ($group) {
                $total_store_sales = $group->sum('store_sales_cash');
                $total_online_sales = $group->sum('online_sales_cash');
                $total_difference = $group->sum('difference');

                return [
                    'cuts' => $group,
                    'total_store_sales' => $total_store_sales,
                    'total_online_sales' => $total_online_sales,
                    'total_sales' => $total_online_sales + $total_store_sales,
                    'total_difference' => $total_difference
                ];
            })->take(7);

        return inertia('CashRegister/Index', compact('cash_registers', 'cash_cuts', 'total_cash_cuts'));
    }


    public function create()
    {
        $total_cash_registers = CashRegister::where('store_id', auth()->user()->store_id)->get()->count();

        return inertia('CashRegister/Create', compact('total_cash_registers'));
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

        //recupera el total de cajas para pasarlo como parametro en url y seleccionar la pestaña correspondiente a historial de cortes
        $total_cash_registers = CashRegister::where('store_id', auth()->user()->store_is)->get()->count();

        return to_route('cash-registers.index', ['tab' => ($total_cash_registers + 1)]);
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

        // Si se esta deshabilitando la caja buscar a los usuarios que tengan esa caja asignada y desasignarla
        if (!$request->is_active) {
            $cash_register_user_asigned = User::where('store_id', auth()->user()->store_id)->where('cash_register_id', $cash_register->id)->first();

            if ($cash_register_user_asigned) {
                $cash_register_user_asigned->update(['cash_register_id' => null]);
            }
        }

        $cash_register->update($validated);
    }


    public function destroy(CashRegister $cash_register)
    {
        //busca si hay ya otro usuario con la caja asignada
        $cash_register_user_asigned = User::where('store_id', auth()->user()->store_id)->where('cash_register_id', $cash_register->id)->first();

        // si hay, le des asigna la caja para no eliminar tambien al usuario.
        if ($cash_register_user_asigned) {
            $cash_register_user_asigned->update(['cash_register_id' => null]);
        }

        $cash_register->delete();
    }


    public function fetchCashRegister($cash_register_id)
    {
        //recupera la primera caja registradora de la tienda para mandar su info como current_cash
        $cash_register = CashRegister::find($cash_register_id);

        return response()->json(['item' => $cash_register]);
    }


    public function asignCashRegister(User $user, $cash_register_id)
    {
        //busca si hay ya otro usuario con la caja asignada
        $cash_register_user_asigned = User::where('store_id', auth()->user()->store_id)->where('cash_register_id', $cash_register_id)->first();

        // si hay, le des asigna la caja y la asigna al usuario que ha hecho la petición.
        if ($cash_register_user_asigned) {
            $cash_register_user_asigned->update(['cash_register_id' => null]);
        }

        //asignación de caja
        $user->update([
            'cash_register_id' => $cash_register_id
        ]);
    }

    public function sendMaxCashNotification()
    {
        $firstUser = auth()->user()->store->users()->first();
        $user_name = auth()->user()->name;
        $firstUser->notify(new BasicEmailNotification(
            "Hacer corte de caja",
            "El usuario $user_name ha solicitado que se realice el corte de caja. Por favor, atiende la solicitud lo antes posible o ponte en contacto con él/ella.",
            route('sales.point')
        ));
    }
}
