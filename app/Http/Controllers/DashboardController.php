<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\OnlineSale;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        return inertia('Dashboard');
    }

    public function getDayData($date)
    {
        $prev_date = Carbon::parse($date)->subDay()->toDateString();
        $sales = Sale::where('store_id', auth()->user()->store_id)->whereDate('created_at', $date)->get();
        $last_period_sales = Sale::where('store_id', auth()->user()->store_id)->whereDate('created_at', $prev_date)->get();
        $expenses = Expense::where('store_id', auth()->user()->store_id)->whereDate('created_at', $date)->get();
        $last_period_expenses = Expense::where('store_id', auth()->user()->store_id)->whereDate('created_at', $prev_date)->get();
        // activar solo si la tienda tiene el paquete para hacer ventas en linea
        $online_sales = OnlineSale::where('store_id', auth()->user()->store_id)
            ->whereNotNull('delivered_at')
            ->whereDate('created_at', $date)
            ->get();
        $last_period_online_sales = OnlineSale::where('store_id', auth()->user()->store_id)
            ->whereNotNull('delivered_at')
            ->whereDate('created_at', $prev_date)
            ->get();
        // $top_products = Sale::with('saleable')
        //     ->select('saleable_id', DB::raw('SUM(quantity) as total_quantity'))
        //     ->where('store_id', auth()->user()->store_id)
        //     ->whereDate('created_at', $date)
        //     ->groupBy('saleable_id')
        //     ->orderByDesc('total_quantity')
        //     ->take(5)
        //     ->get();

        // cargar los datos del producto asociado
        // $top_products->load('saleable');
        // return $top_products;
        $top_products = [];

        return response()->json(compact(
            'sales',
            'last_period_sales',
            'top_products',
            'expenses',
            'last_period_expenses',
            'online_sales',
            'last_period_online_sales',
        ));
    }

    public function getWeekData($date)
    {
        $prev_date = Carbon::parse($date)->subWeek();
        $date = Carbon::parse($date);

        // Ajustar el inicio de la semana al domingo y el final de la semana al sábado
        $startOfWeek = $date->copy()->startOfWeek(Carbon::SUNDAY)->toDateString();
        $endOfWeek = $date->copy()->endOfWeek(Carbon::SATURDAY)->toDateString();

        // Ventas y gastos de la semana seleccionada
        $sales = Sale::where('store_id', auth()->user()->store_id)
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->get();

        $last_period_sales = Sale::where('store_id', auth()->user()->store_id)
            ->whereBetween('created_at', [
                Carbon::parse($prev_date)->startOfWeek(Carbon::SUNDAY)->toDateString(),
                Carbon::parse($prev_date)->endOfWeek(Carbon::SATURDAY)->toDateString()
            ])->get();

        $online_sales = OnlineSale::where('store_id', auth()->user()->store_id)
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->whereNotNull('delivered_at')
            ->get();

        $last_period_online_sales = OnlineSale::where('store_id', auth()->user()->store_id)
            ->whereNotNull('delivered_at')
            ->whereBetween('created_at', [
                Carbon::parse($prev_date)->startOfWeek(Carbon::SUNDAY)->toDateString(),
                Carbon::parse($prev_date)->endOfWeek(Carbon::SATURDAY)->toDateString()
            ])->get();

        $expenses = Expense::where('store_id', auth()->user()->store_id)
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->get();

        $last_period_expenses = Expense::where('store_id', auth()->user()->store_id)
            ->whereBetween('created_at', [
                Carbon::parse($prev_date)->startOfWeek(Carbon::SUNDAY)->toDateString(),
                Carbon::parse($prev_date)->endOfWeek(Carbon::SATURDAY)->toDateString()
            ])->get();

        // $top_products = Sale::select('product_id', DB::raw('SUM(quantity) as total_quantity'))
        //     ->where('store_id', auth()->user()->store_id)
        //     ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
        //     ->groupBy('product_id')
        //     ->orderByDesc('total_quantity')
        //     ->take(5)
        //     ->get();

        // Cargar los datos del producto asociado
        // $top_products->load('product');
        $top_products = [];

        return response()->json(compact('sales', 'last_period_sales', 'online_sales', 'last_period_online_sales', 'top_products', 'expenses', 'last_period_expenses'));
    }


    public function getMonthData($date)
    {
        $current_month = Carbon::parse($date);
        $prev_month = Carbon::parse($date)->subMonth();

        $sales = Sale::whereYear('created_at', $current_month->year)
            ->where('store_id', auth()->user()->store_id)
            ->whereMonth('created_at', $current_month->month)
            ->get();

        $last_period_sales = Sale::whereYear('created_at', $prev_month->year)
            ->where('store_id', auth()->user()->store_id)
            ->whereMonth('created_at', $prev_month->month)
            ->get();

        $online_sales = OnlineSale::whereYear('created_at', $current_month->year)
            ->where('store_id', auth()->user()->store_id)
            ->whereNotNull('delivered_at')
            ->whereMonth('created_at', $current_month->month)
            ->get();

        $last_period_online_sales = OnlineSale::whereYear('created_at', $prev_month->year)
            ->where('store_id', auth()->user()->store_id)
            ->whereNotNull('delivered_at')
            ->whereMonth('created_at', $prev_month->month)
            ->get();

        $expenses = Expense::whereYear('created_at', $current_month->year)
            ->where('store_id', auth()->user()->store_id)
            ->whereMonth('created_at', $current_month->month)
            ->get();

        $last_period_expenses = Expense::whereYear('created_at', $prev_month->year)
            ->where('store_id', auth()->user()->store_id)
            ->whereMonth('created_at', $prev_month->month)
            ->get();

        // $top_products = Sale::select('product_id', DB::raw('SUM(quantity) as total_quantity'))
        //     ->whereYear('created_at', $current_month->year)
        //     ->whereMonth('created_at', $current_month->month)
        //     ->groupBy('product_id')
        //     ->orderByDesc('total_quantity')
        //     ->take(5)
        //     ->get();

        // Puedes cargar los datos del producto asociado si lo necesitas
        // $top_products->load('product');
        $top_products = [];

        return response()->json(compact('sales', 'last_period_sales', 'online_sales', 'last_period_online_sales', 'top_products', 'expenses', 'last_period_expenses'));
    }
}
