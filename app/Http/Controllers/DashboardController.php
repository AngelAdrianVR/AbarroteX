<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\OnlineSale;
use App\Models\Sale;
use App\Models\ServiceReport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class DashboardController extends Controller
{
    public function index()
    {
        return inertia('Dashboard');
    }

    /**
     * Genera un reporte de ingresos y gastos en formato Excel.
     */
    public function generateReport(Request $request)
    {
        // --- 1. VALIDACIÓN DE LA SOLICITUD ---
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($validated['start_date'])->startOfDay();
        $endDate = Carbon::parse($validated['end_date'])->endOfDay();

        // --- 2. OBTENCIÓN DE DATOS ---
        $sales = Sale::whereBetween('created_at', [$startDate, $endDate])->get();
        $serviceAdvances = ServiceReport::whereNotNull('advance_payment')
            ->where('advance_payment', '>', 0)
            ->where('status', '!=', 'Entregado/Pagado')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
        $settledServices = ServiceReport::where('status', 'Entregado/Pagado')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->get();
        $expenses = Expense::whereBetween('created_at', [$startDate, $endDate])->get();

        // --- 3. PROCESAMIENTO DE DATOS ---
        $totals = [
            'income' => ['Efectivo' => 0, 'Tarjeta' => 0, 'Transferencia' => 0, 'total' => 0],
            'expenses' => ['Efectivo' => 0, 'Tarjeta' => 0, 'Transferencia' => 0, 'total' => 0],
        ];

        foreach ($sales as $sale) {
            $amount = $sale->quantity * $sale->current_price;
            if (isset($totals['income'][$sale->payment_method])) {
                $totals['income'][$sale->payment_method] += $amount;
            }
        }
        foreach ($serviceAdvances as $service) {
            if (isset($totals['income'][$service->payment_method])) {
                $totals['income'][$service->payment_method] += $service->advance_payment;
            }
        }
        foreach ($settledServices as $service) {
            $settlementAmount = $service->total_cost - $service->advance_payment;
            if (isset($totals['income'][$service->payment_method])) {
                $totals['income'][$service->payment_method] += $settlementAmount;
            }
        }
        foreach ($expenses as $expense) {
            $amount = $expense->quantity * $expense->current_price;
            if (isset($totals['expenses'][$expense->payment_method])) {
                $totals['expenses'][$expense->payment_method] += $amount;
            }
        }

        $totals['income']['total'] = $totals['income']['Efectivo'] + $totals['income']['Tarjeta'] + $totals['income']['Transferencia'];
        $totals['expenses']['total'] = $totals['expenses']['Efectivo'] + $totals['expenses']['Tarjeta'] + $totals['expenses']['Transferencia'];

        $balance = [
            'Efectivo' => $totals['income']['Efectivo'] - $totals['expenses']['Efectivo'],
            'Tarjeta' => $totals['income']['Tarjeta'] - $totals['expenses']['Tarjeta'],
            'Transferencia' => $totals['income']['Transferencia'] - $totals['expenses']['Transferencia'],
            'total' => $totals['income']['total'] - $totals['expenses']['total'],
        ];

        // --- 4. CREACIÓN DEL ARCHIVO EXCEL ---
        $spreadsheet = new Spreadsheet();

        // --- ESTILOS REUTILIZABLES ---
        $currencyFormat = '$ #,##0.00';
        $headerStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFF2F2F2'],
            ],
        ];

        // ======== HOJA 1: REPORTE GENERAL ========
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Reporte General');

        $sheet->mergeCells('A1:F1');
        $sheet->setCellValue('A1', 'Reporte de Ingresos y Gastos del ' . $startDate->format('d/m/Y') . ' al ' . $endDate->format('d/m/Y'));
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // -- SECCIÓN DE RESUMEN --
        $sheet->setCellValue('A3', 'Resumen General');
        $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(14);

        $summaryHeaders = ['Concepto', 'Efectivo', 'Tarjeta', 'Transferencia', 'Total'];
        $sheet->fromArray($summaryHeaders, NULL, 'A4');
        $sheet->getStyle('A4:E4')->applyFromArray($headerStyle);

        $sheet->setCellValue('A5', 'Ingresos');
        $sheet->setCellValue('B5', $totals['income']['Efectivo']);
        $sheet->setCellValue('C5', $totals['income']['Tarjeta']);
        $sheet->setCellValue('D5', $totals['income']['Transferencia']);
        $sheet->setCellValue('E5', $totals['income']['total']);

        $sheet->setCellValue('A6', 'Gastos');
        $sheet->setCellValue('B6', $totals['expenses']['Efectivo']);
        $sheet->setCellValue('C6', $totals['expenses']['Tarjeta']);
        $sheet->setCellValue('D6', $totals['expenses']['Transferencia']);
        $sheet->setCellValue('E6', $totals['expenses']['total']);

        $sheet->setCellValue('A7', 'Balance (Utilidad)');
        $sheet->setCellValue('B7', $balance['Efectivo']);
        $sheet->setCellValue('C7', $balance['Tarjeta']);
        $sheet->setCellValue('D7', $balance['Transferencia']);
        $sheet->setCellValue('E7', $balance['total']);
        $sheet->getStyle('A7:E7')->getFont()->setBold(true);
        $sheet->getStyle('B5:E7')->getNumberFormat()->setFormatCode($currencyFormat);

        $currentRow = 9;

        // -- SECCIÓN DE DETALLES DE INGRESOS --
        if ($sales->count() > 0) {
            $sheet->setCellValue('A' . $currentRow, 'Detalle de Ventas');
            $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true)->setSize(14);
            $currentRow++;
            $sheet->fromArray(['Fecha', 'Folio', 'Producto', 'Cantidad', 'Precio Total', 'Método de Pago'], NULL, 'A' . $currentRow);
            $sheet->getStyle('A' . $currentRow . ':F' . $currentRow)->applyFromArray($headerStyle);
            $currentRow++;
            foreach ($sales as $sale) {
                $sheet->fromArray([
                    $sale->created_at->format('d/m/Y H:i'),
                    $sale->folio,
                    $sale->product_name,
                    $sale->quantity,
                    $sale->quantity * $sale->current_price,
                    $sale->payment_method
                ], NULL, 'A' . $currentRow);
                $sheet->getStyle('E' . $currentRow)->getNumberFormat()->setFormatCode($currencyFormat);
                $currentRow++;
            }
            $currentRow++;
        }

        if ($serviceAdvances->count() > 0) {
            $sheet->setCellValue('A' . $currentRow, 'Detalle de Anticipos de Servicios');
            $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true)->setSize(14);
            $currentRow++;
            $sheet->fromArray(['Fecha Registro', 'Folio', 'Cliente', 'Descripción', 'Monto Anticipo', 'Método de Pago'], NULL, 'A' . $currentRow);
            $sheet->getStyle('A' . $currentRow . ':F' . $currentRow)->applyFromArray($headerStyle);
            $currentRow++;
            foreach ($serviceAdvances as $service) {
                $sheet->fromArray([
                    $service->created_at->format('d/m/Y H:i'),
                    $service->folio,
                    $service->client_name,
                    $service->service_description,
                    $service->advance_payment,
                    $service->payment_method
                ], NULL, 'A' . $currentRow);
                $sheet->getStyle('E' . $currentRow)->getNumberFormat()->setFormatCode($currencyFormat);
                $currentRow++;
            }
            $currentRow++;
        }

        if ($settledServices->count() > 0) {
            $sheet->setCellValue('A' . $currentRow, 'Detalle de Servicios Liquidados');
            $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true)->setSize(14);
            $currentRow++;
            $sheet->fromArray(['Fecha de Pago', 'Folio', 'Cliente', 'Descripción', 'Monto Liquidado', 'Método de Pago'], NULL, 'A' . $currentRow);
            $sheet->getStyle('A' . $currentRow . ':F' . $currentRow)->applyFromArray($headerStyle);
            $currentRow++;
            foreach ($settledServices as $service) {
                $settlementAmount = $service->total_cost - $service->advance_payment;
                $sheet->fromArray([
                    $service->paid_at->format('d/m/Y H:i'),
                    $service->folio,
                    $service->client_name,
                    $service->service_description,
                    $settlementAmount,
                    $service->payment_method
                ], NULL, 'A' . $currentRow);
                $sheet->getStyle('E' . $currentRow)->getNumberFormat()->setFormatCode($currencyFormat);
                $currentRow++;
            }
            $currentRow++;
        }

        foreach (range('A', 'F') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // ======== HOJA 2: DETALLE DE GASTOS ========
        if ($expenses->count() > 0) {
            $expenseSheet = $spreadsheet->createSheet();
            $expenseSheet->setTitle('Detalle de Gastos');

            $expenseSheet->setCellValue('A1', 'Detalle de Gastos');
            $expenseSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

            $expenseHeaders = ['Fecha', 'Concepto', 'Cantidad', 'Costo Total', 'Método de Pago'];
            $expenseSheet->fromArray($expenseHeaders, NULL, 'A2');
            $expenseSheet->getStyle('A2:E2')->applyFromArray($headerStyle);

            $expenseRow = 3;
            foreach ($expenses as $expense) {
                $expenseSheet->fromArray([
                    $expense->created_at->format('d/m/Y H:i'),
                    $expense->concept,
                    $expense->quantity,
                    $expense->quantity * $expense->current_price,
                    $expense->payment_method
                ], NULL, 'A' . $expenseRow);
                $expenseSheet->getStyle('D' . $expenseRow)->getNumberFormat()->setFormatCode($currencyFormat);
                $expenseRow++;
            }

            foreach (range('A', 'E') as $columnID) {
                $expenseSheet->getColumnDimension($columnID)->setAutoSize(true);
            }
        }

        // --- 5. ENVIAR EL ARCHIVO AL NAVEGADOR ---
        $spreadsheet->setActiveSheetIndex(0); // Asegurarse de que la primera hoja esté activa al abrir

        $fileName = "Reporte_Ingresos_Gastos_" . now()->format('Y-m-d') . ".xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
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
            ->whereDate('created_at', $date)
            ->where('status', 'Entregado/Pagado')
            ->get();

        $last_period_completed_service_orders = ServiceReport::where('store_id', auth()->user()->store_id)
            ->whereDate('created_at', $prev_date)
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

        return response()->json(compact(
            'sales',
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


        return response()->json(compact(
            'sales',
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
