<?php

namespace App\Http\Controllers;

use App\Models\CashRegisterMovement;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        return inertia('Expense/Index');
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
                'payment_method' => $expenseData['payment_method'],
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

    public function show($date)
    {
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
            'payment_method' => 'required|string|max:255',
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
        // eliminar movimiento de caja si es que lo tiene
        $expense->cashRegisterMovement?->delete();
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

    public function getDataForTable()
    {
        $perPage = request('pageSize', 100);
        $page = request('page', 1);

        $expenses = Expense::where('store_id', auth()->user()->store_id)->latest()->get();

        // Agrupar las ventas por fecha con el nuevo formato de fecha y calcular el total de productos vendidos y el total de ventas para cada fecha
        $groupedExpenses = $expenses->groupBy(function ($expense) {
            return Carbon::parse($expense->created_at)->format('d-F-Y');
        })->map(function ($expenses) {
            $date = $expenses[0]['created_at'];
            $totalQuantity = $expenses->sum('quantity');
            $totalExpense = $expenses->sum(function ($expense) {
                return $expense->quantity * $expense->current_price;
            });

            return [
                'date' => $date,
                'total_quantity' => $totalQuantity,
                'total_expense' => $totalExpense,
                'expenses' => $expenses,
            ];
        });

        // Paginación manual
        $paginatedItems = $groupedExpenses->forPage($page, $perPage);
        $total = $groupedExpenses->count();

        $data = [
            'items' => $paginatedItems->values(),
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
            ]
        ];

        return response()->json(compact('data'));
    }
}
