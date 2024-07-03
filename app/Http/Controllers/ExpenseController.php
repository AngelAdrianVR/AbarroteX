<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\CashRegisterMovement;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{

    public function index()
    {
        // Obtener todos los gastos registrados y contar el número de agrupaciones por día
        $total_expenses = DB::table('expenses')
            ->select(DB::raw('DATE(created_at) as date'))
            ->where('store_id', auth()->user()->store_id)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get()
            ->count();

        // Calcular la fecha hace x días para recuperar los gastos de x dias atras hasta la fecha de hoy
        // $days_ago = Carbon::now()->subDays(30);
        // Obtener los gastos registrados en los últimos 7 días
        // $expenses = Expense::where('store_id', auth()->user()->store_id)->whereDate('created_at', '>=', $days_ago)->latest()->get();
        $expenses = Expense::where('store_id', auth()->user()->store_id)->latest()->get();

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
        })->take(30);

        return inertia('Expense/Index', compact('groupedExpenses', 'total_expenses'));
    }

    public function create()
    {
        //recupera la primera caja registradora de la tienda para mandar su info como current_cash
        $cash_register = auth()->user()->cashRegister;

        return inertia('Expense/Create', compact('cash_register'));
    }

    public function store(Request $request)
    {
        // Itera sobre cada gasto recibido en la lista
        foreach ($request->expenses as $expenseData) {

            // Convierte el campo 'creado el' a un objeto Carbon y resta 6 horas
            // $created_at = Carbon::parse($expenseData['date'])->subHours(6);
            $expense = Expense::create([
                'concept' => $expenseData['concept'],
                'quantity' => $expenseData['quantity'],
                'current_price' => $expenseData['current_price'],
                'amount_from_cash_register' => $expenseData['from_cash_register'] ? $expenseData['current_price'] : null,
                'store_id' => auth()->user()->store_id,
                // 'created_at' => now(),
            ]);

            // crear retiro de dinero en caja si el dinero se toma de ahi
            if ($expenseData['from_cash_register']) {
                // obtiene la caja registradora del usuario autenticado
                $cash_register = auth()->user()->cashRegister;
                // Crea el movimiento de la caja obtenida anteriormente. En caso de haber varias cajas ajustar lógica
                CashRegisterMovement::create([
                    'amount' => $expenseData['current_price'],
                    'type' => 'Retiro',
                    'notes' => 'Registro de gasto: ' . $expenseData['concept'] ?? 'Sin concepto',
                    'cash_register_id' => $cash_register->id,
                    'expense_id' => $expense->id,
                ]);
                //actualizar el dinero actual de la caja
                $cash_register->decrement('current_cash', $expenseData['current_price']);
            }
        }
        return to_route('expenses.index');
    }

    public function show($encoded_expense_id)
    {
        // Decodificar el ID
        $decoded_expense_id = base64_decode($encoded_expense_id);

        $expense = Expense::find($decoded_expense_id);

        // Parsear la fecha recibida para obtener solo la parte de la fecha
        $date = Carbon::parse($expense->created_at)->toDateString();

        // Obtener las ventas registradas en la fecha recibida
        $expenses = Expense::where('store_id', auth()->user()->store_id)->whereDate('created_at', $date)->get();

        return inertia('Expense/Show', compact('expenses'));
    }

    public function edit(Expense $expense)
    {
        //
    }

    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'concept' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:1',
            'current_price' => 'required|numeric|min:0',
        ]);

        $expense->update($validated);

        // actualizar movimiento de caja relacionada si es que la hay
        $expense->cashRegisterMovement?->update([
            'notes' => $expense->concept,
            'amount' => $expense->current_price * $expense->quantity,
        ]);
    }

    public function destroy(Expense $expense)
    {
        // Eliminar el registro de gasto enviado como referencia
        $expense->delete();
    }

    public function destroyDayExpenses(Expense $expense)
    {
        // Obtener la fecha de creación del registro de gasto
        $expenseDate = $expense->created_at->toDateString();

        // Eliminar todos los registros que tengan la misma fecha de creación
        Expense::where('store_id', auth()->user()->store_id)->whereDate('created_at', $expenseDate)->delete();

        // Eliminar el registro de gasto enviado como referencia
        $expense->delete();
    }

    public function filterExpenses(Request $request)
    {
        $queryDate = $request->input('queryDate');
        $startDate = Carbon::parse($queryDate[0])->startOfDay();
        $endDate = Carbon::parse($queryDate[1])->endOfDay();

        // Obtener los gastos registrados en el rango de fechas requerido por el filtro
        $expenses = Expense::where('store_id', auth()->user()->store_id)->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate)->latest()->get();

        // Agrupar los gastos por fecha con el nuevo formato de fecha y calcular el total de productos vendidos y el total de ventas para cada fecha
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
        $offset = $currentPage * 30;
        // $offset = 30 + $currentPage * 30; //multiplica por 4 para traer de 4 dias en 4 dias. suma 4 dias porque son los que ya se cargaron
        // $skip_days = $currentPage * 30; //multiplica por 4 para traer de 4 dias en 4 dias
        // Calcular la fecha hace x días para recuperar las ventas de x dias atras hasta la fecha de hoy
        // $days_ago = Carbon::now()->subDays($offset);
        // ignorar esa cantidad de dias porque ya se cargaron.
        // $days_befor = Carbon::now()->subDays($skip_days);
        // Obtener las ventas registradas en los últimos 7 días
        // $expenses = Expense::where('store_id', auth()->user()->store_id)->whereDate('created_at', '>=', $days_ago)->whereDate('created_at', '<=', $days_befor)->latest()->get();

        $expenses = Expense::where('store_id', auth()->user()->store_id)->latest()->get();

        // Agrupar los gastos por fecha con el nuevo formato de fecha y calcular el total de productos vendidos y el total de ventas para cada fecha
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
        })->skip($offset)
            ->take(30);

        return response()->json(['items' => $groupedExpenses]);
    }

    public function printExpenses($expense_id)
    {
        // $expense = Expense::with('products')->find($expense_id);
        $expense = null;

        return inertia('Expense/PrintExpenses', compact('expense'));
    }
}
