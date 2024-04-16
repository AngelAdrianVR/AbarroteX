<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        return inertia('Dashboard');
    }
    
    public function getDayData()
    {
        $sales = Sale::with('product', 'globalProductStore.globalProduct')->where('store_id', auth()->user()->store_id)->whereDate('created_at', today())->get();
        $last_period_sales = Sale::with('product', 'globalProductStore.globalProduct')->where('store_id', auth()->user()->store_id)->whereDate('created_at', today()->subDay())->get();
        $expenses = Expense::where('store_id', auth()->user()->store_id)->whereDate('created_at', today())->get();
        $last_period_expenses = Expense::where('store_id', auth()->user()->store_id)->whereDate('created_at', today()->subDay())->get();
        $top_products = Sale::select('product_id', DB::raw('SUM(quantity) as total_quantity'))
            ->where('store_id', auth()->user()->store_id)
            ->whereDate('created_at', today())
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->take(5)
            ->get();

        // cargar los datos del producto asociado
        $top_products->load('product');

        return response()->json(compact('sales', 'last_period_sales', 'top_products', 'expenses', 'last_period_expenses'));
    }

    public function getWeekData()
    {
        // Ventas y egresos de la semana en curso
        $sales = Sale::with('product', 'globalProductStore.globalProduct', 'globalProductStore.globalProduct')->where('store_id', auth()->user()->store_id)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->get();
        $last_period_sales = Sale::with('product', 'globalProductStore.globalProduct')->where('store_id', auth()->user()->store_id)->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->get();
        $expenses = Expense::where('store_id', auth()->user()->store_id)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->get();
        $last_period_expenses = Expense::where('store_id', auth()->user()->store_id)->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->get();
        $top_products = Sale::select('product_id', DB::raw('SUM(quantity) as total_quantity'))
            ->where('store_id', auth()->user()->store_id)
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->take(5)
            ->get();

        // cargar los datos del producto asociado
        $top_products->load('product');

        return response()->json(compact('sales', 'last_period_sales', 'top_products', 'expenses', 'last_period_expenses'));
    }

    public function getMonthData()
    {
        $sales = Sale::with('product', 'globalProductStore.globalProduct')->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->get();
        $last_period_sales = Sale::with('product', 'globalProductStore.globalProduct')->whereYear('created_at', now()->subMonth()->year)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->get();
        $expenses = Expense::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)->get();
        $last_period_expenses = Expense::whereYear('created_at', now()->subMonth()->year)
            ->whereMonth('created_at', now()->subMonth()->month)->get();
        $top_products = Sale::select('product_id', DB::raw('SUM(quantity) as total_quantity'))
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->take(5)
            ->get();

        // Puedes cargar los datos del producto asociado si lo necesitas
        $top_products->load('product');

        return response()->json(compact('sales', 'last_period_sales', 'top_products', 'expenses', 'last_period_expenses'));
    }
}
