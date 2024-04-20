<?php

namespace App\Http\Controllers;

use App\Models\Expense;
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
        $sales = Sale::with('product', 'globalProductStore.globalProduct')->where('store_id', auth()->user()->store_id)->whereDate('created_at', $date)->get();
        $last_period_sales = Sale::with('product', 'globalProductStore.globalProduct')->where('store_id', auth()->user()->store_id)->whereDate('created_at', $prev_date)->get();
        $expenses = Expense::where('store_id', auth()->user()->store_id)->whereDate('created_at', $date)->get();
        $last_period_expenses = Expense::where('store_id', auth()->user()->store_id)->whereDate('created_at', $prev_date)->get();
        $top_products = Sale::select('product_id', DB::raw('SUM(quantity) as total_quantity'))
            ->where('store_id', auth()->user()->store_id)
            ->whereDate('created_at', $date)
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->take(5)
            ->get();

        // cargar los datos del producto asociado
        $top_products->load('product');

        return response()->json(compact('sales', 'last_period_sales', 'top_products', 'expenses', 'last_period_expenses'));
    }

    public function getWeekData($date)
    {
        $prev_date = Carbon::parse($date)->subWeek();
        $date = Carbon::parse($date);

        // Ajustar el inicio de la semana al domingo y el final de la semana al sábado
        $startOfWeek = $date->copy()->startOfWeek(Carbon::SUNDAY)->toDateString();
        $endOfWeek = $date->copy()->endOfWeek(Carbon::SATURDAY)->toDateString();

        // Ventas y egresos de la semana seleccionada
        $sales = Sale::with('product', 'globalProductStore.globalProduct', 'globalProductStore.globalProduct')
            ->where('store_id', auth()->user()->store_id)
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->get();

        $last_period_sales = Sale::with('product', 'globalProductStore.globalProduct')
            ->where('store_id', auth()->user()->store_id)
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

        $top_products = Sale::select('product_id', DB::raw('SUM(quantity) as total_quantity'))
            ->where('store_id', auth()->user()->store_id)
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->take(5)
            ->get();

        // Cargar los datos del producto asociado
        $top_products->load('product');

        return response()->json(compact('sales', 'last_period_sales', 'top_products', 'expenses', 'last_period_expenses'));
    }


    public function getMonthData($date)
    {
        $current_month = Carbon::parse($date);
        $prev_month = Carbon::parse($date)->subMonth();

        $sales = Sale::with('product', 'globalProductStore.globalProduct')
            ->whereYear('created_at', $current_month->year)
            ->whereMonth('created_at', $current_month->month)
            ->get();

        $last_period_sales = Sale::with('product', 'globalProductStore.globalProduct')
            ->whereYear('created_at', $prev_month->year)
            ->whereMonth('created_at', $prev_month->month)
            ->get();

        $expenses = Expense::whereYear('created_at', $current_month->year)
            ->whereMonth('created_at', $current_month->month)
            ->get();

        $last_period_expenses = Expense::whereYear('created_at', $prev_month->year)
            ->whereMonth('created_at', $prev_month->month)
            ->get();

        $top_products = Sale::select('product_id', DB::raw('SUM(quantity) as total_quantity'))
            ->whereYear('created_at', $current_month->year)
            ->whereMonth('created_at', $current_month->month)
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->take(5)
            ->get();

        // Puedes cargar los datos del producto asociado si lo necesitas
        $top_products->load('product');

        return response()->json(compact('sales', 'last_period_sales', 'top_products', 'expenses', 'last_period_expenses'));
    }
}
