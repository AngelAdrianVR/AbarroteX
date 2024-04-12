<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{

    public function index()
    {
        // Obtener todos los egresos registrados y contar el número de agrupaciones por día
        $total_expenses = DB::table('expenses')
            ->select(DB::raw('DATE(created_at) as date'))
            ->where('store_id', auth()->user()->store_id)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get()
            ->count();


        // Calcular la fecha hace x días para recuperar los egresos de x dias atras hasta la fecha de hoy
        $days_ago = Carbon::now()->subDays(4);

        // Obtener los egresos registrados en los últimos 7 días
        $expenses = Expense::where('store_id', auth()->user()->store_id)->whereDate('created_at', '>=', $days_ago)->latest()->get();

        // Agrupar las ventas por fecha con el nuevo formato de fecha y calcular el total de productos vendidos y el total de ventas para cada fecha
        $groupedExpenses = $expenses->groupBy(function ($expense) {
            return Carbon::parse($expense->created_at)->format('d-F-Y');
        })->map(function ($expenses) {
            $totalQuantity = $expenses->sum('quantity');
            $totalExpense = $expenses->sum(function ($expense) {
                return $expense->quantity * $expense->current_price;
            });

            return [
                'total_quantity' => $totalQuantity,
                'total_expense' => $totalExpense,
                'expenses' => $expenses,
            ];
        });
            
        // return $groupedExpenses;
        return inertia('Expense/Index', compact('groupedExpenses', 'total_expenses'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $request->validate([
            'concept' => 'required|string|max:255',
            'quantity' => 'required|numeric|max:999999.99',
            'cost' => 'required|numeric|max:999999.99',
        ]);
    }

    public function show($created_at)
    {
        // Parsear la fecha recibida para obtener solo la parte de la fecha
        $date = Carbon::parse($created_at)->toDateString();

        // Obtener las ventas registradas en la fecha recibida
        $expenses = Expense::where('store_id', auth()->user()->store_id)->whereDate('created_at', $date)->get();

        // return $expenses;

        $day_expenses = null;
        return inertia('Expense/Show', compact('day_expenses'));
    }

    public function edit(Expense $expense)
    {
        //
    }

    public function update(Request $request, Expense $expense)
    {
        //
    }

    public function destroy(Expense $expense)
    {
        // Obtener la fecha de creación del registro de egreso
        $expenseDate = $expense->created_at->toDateString();

        // Eliminar todos los registros que tengan la misma fecha de creación
        Expense::where('store_id', auth()->user()->store_id)->whereDate('created_at', $expenseDate)->delete();

        // Eliminar el registro de egreso enviado como referencia
        $expense->delete();
    }

    public function filterExpenses(Request $request)
    {
        $queryDate = $request->input('queryDate');
        $startDate = Carbon::parse($queryDate[0])->startOfDay();
        $endDate = Carbon::parse($queryDate[1])->endOfDay();

        // Obtener los egresos registrados en el rango de fechas requerido por el filtro
        $expenses = Expense::where('store_id', auth()->user()->store_id)->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate)->latest()->get();

        // Agrupar los egresos por fecha con el nuevo formato de fecha y calcular el total de productos vendidos y el total de ventas para cada fecha
        $groupedExpenses = $expenses->groupBy(function ($expense) {
            return Carbon::parse($expense->created_at)->format('d-F-Y');
        })->map(function ($expenses) {
            $totalQuantity = $expenses->sum('quantity');
            $totalExpense = $expenses->sum(function ($expense) {
                return $expense->quantity * $expense->current_price;
            });

            return [
                'total_quantity' => $totalQuantity,
                'total_expense' => $totalExpense,
                'expenses' => $expenses,
            ];
        });

        return response()->json(['items' => $groupedExpenses]);
    }

    public function getItemsByPage($currentPage)
    {
        $offset = 4 + $currentPage * 4; //multiplica por 4 para traer de 4 dias en 4 dias. suma 4 dias porque son los que ya se cargaron
        $skip_days = $currentPage * 4; //multiplica por 4 para traer de 4 dias en 4 dias

         // Calcular la fecha hace x días para recuperar las ventas de x dias atras hasta la fecha de hoy
         $days_ago = Carbon::now()->subDays($offset);
         // ignorar esa cantidad de dias porque ya se cargaron.
         $days_befor = Carbon::now()->subDays($skip_days);

         // Obtener las ventas registradas en los últimos 7 días
         $expenses = Expense::where('store_id', auth()->user()->store_id)->whereDate('created_at', '>=', $days_ago)->whereDate('created_at', '<=', $days_befor)->latest()->get();
 
        // Agrupar los egresos por fecha con el nuevo formato de fecha y calcular el total de productos vendidos y el total de ventas para cada fecha
        $groupedExpenses = $expenses->groupBy(function ($expense) {
            return Carbon::parse($expense->created_at)->format('d-F-Y');
        })->map(function ($expenses) {
            $totalQuantity = $expenses->sum('quantity');
            $totalExpense = $expenses->sum(function ($expense) {
                return $expense->quantity * $expense->current_price;
            });

            return [
                'total_quantity' => $totalQuantity,
                'total_expense' => $totalExpense,
                'expenses' => $expenses,
            ];
        });

        return response()->json(['items' => $groupedExpenses]);
    }

}
