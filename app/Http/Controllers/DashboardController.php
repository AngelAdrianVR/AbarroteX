<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\OnlineSale;
use App\Models\Sale;
use App\Models\ServiceReport;
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

        $online_sales = OnlineSale::where('store_id', auth()->user()->store_id)
            ->whereNotNull('delivered_at')
            ->whereDate('delivered_at', $date)
            ->get();

        $last_period_online_sales = OnlineSale::where('store_id', auth()->user()->store_id)
            ->whereNotNull('delivered_at')
            ->whereDate('delivered_at', $prev_date)
            ->get();

        $top_products = Sale::where('store_id', auth()->user()->store_id)
            ->whereDate('created_at', $date)
            ->select('product_name', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('product_name')
            ->orderByDesc('total_quantity')
            ->take(5)
            ->get();

        // órdenes de servicio completadas
        $completed_service_orders = ServiceReport::where('store_id', auth()->user()->store_id)
            ->whereDate('paid_at', $date)
            ->where('status', 'Entregado/Pagado')
            ->get();

        $last_period_completed_service_orders = ServiceReport::where('store_id', auth()->user()->store_id)
            ->whereDate('paid_at', $prev_date)
            ->where('status', 'Entregado/Pagado')
            ->get();

        return response()->json(compact(
            'sales',
            'last_period_sales',
            'top_products',
            'expenses',
            'last_period_expenses',
            'online_sales',
            'last_period_online_sales',
            'completed_service_orders',
            'last_period_completed_service_orders',
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
            ->whereBetween('delivered_at', [$startOfWeek, $endOfWeek])
            ->whereNotNull('delivered_at')
            ->get();

        $last_period_online_sales = OnlineSale::where('store_id', auth()->user()->store_id)
            ->whereNotNull('delivered_at')
            ->whereBetween('delivered_at', [
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

        $top_products = Sale::select('product_name', DB::raw('SUM(quantity) as total_quantity'))
            ->where('store_id', auth()->user()->store_id)
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->groupBy('product_name')
            ->orderByDesc('total_quantity')
            ->take(5)
            ->get();

        // órdenes de servicio completadas
        $completed_service_orders = ServiceReport::where('store_id', auth()->user()->store_id)
            ->whereBetween('paid_at', [$startOfWeek, $endOfWeek])
            ->where('status', 'Entregado/Pagado')
            ->get();

        $last_period_completed_service_orders = ServiceReport::where('store_id', auth()->user()->store_id)
            ->whereBetween('paid_at', [
                Carbon::parse($prev_date)->startOfWeek(Carbon::SUNDAY)->toDateString(),
                Carbon::parse($prev_date)->endOfWeek(Carbon::SATURDAY)->toDateString()
            ])
            ->where('status', 'Entregado/Pagado')
            ->get();

        return response()->json(compact('sales', 
            'last_period_sales', 
            'online_sales', 
            'last_period_online_sales', 
            'top_products', 'expenses', 
            'last_period_expenses',
            'completed_service_orders',
            'last_period_completed_service_orders',
        ));
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
            ->whereMonth('delivered_at', $current_month->month)
            ->get();

        $last_period_online_sales = OnlineSale::whereYear('created_at', $prev_month->year)
            ->where('store_id', auth()->user()->store_id)
            ->whereNotNull('delivered_at')
            ->whereMonth('delivered_at', $prev_month->month)
            ->get();

        $expenses = Expense::whereYear('created_at', $current_month->year)
            ->where('store_id', auth()->user()->store_id)
            ->whereMonth('created_at', $current_month->month)
            ->get();

        $last_period_expenses = Expense::whereYear('created_at', $prev_month->year)
            ->where('store_id', auth()->user()->store_id)
            ->whereMonth('created_at', $prev_month->month)
            ->get();

        $top_products = Sale::select('product_name', DB::raw('SUM(quantity) as total_quantity'))
            ->whereYear('created_at', $current_month->year)
            ->whereMonth('created_at', $current_month->month)
            ->where('store_id', auth()->user()->store_id)
            ->groupBy('product_name')
            ->orderByDesc('total_quantity')
            ->take(5)
            ->get();

        // órdenes de servicio completadas
        $completed_service_orders = ServiceReport::where('store_id', auth()->user()->store_id)
            ->whereYear('paid_at', $current_month->year)
            ->whereMonth('paid_at', $current_month->month)
            ->where('status', 'Entregado/Pagado')
            ->get();

        $last_period_completed_service_orders = ServiceReport::where('store_id', auth()->user()->store_id)
            ->whereYear('paid_at', $prev_month->year)
            ->whereMonth('paid_at', $prev_month->month)
            ->where('status', 'Entregado/Pagado')
            ->get();
        

        return response()->json(compact('sales', 
            'last_period_sales', 
            'online_sales', 
            'last_period_online_sales', 
            'top_products', 
            'expenses', 
            'last_period_expenses',
            'completed_service_orders',
            'last_period_completed_service_orders',
        ));
    }
}
