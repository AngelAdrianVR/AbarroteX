<?php

namespace App\Http\Controllers;

use App\Models\CashRegisterMovement;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Service;
use App\Models\ServiceReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use App\Services\TinifyService;
use Illuminate\Support\Facades\Log;

class ServiceReportController extends Controller
{
    public function __construct(protected TinifyService $tinifyService) {}

    public function index()
    {
        return inertia('ServiceReport/Index');
    }

    public function create()
    {
        $products = Product::where('store_id', auth()->user()->store_id)->get(['id', 'name', 'code', 'description']);
        $store_id = auth()->user()->store_id;
        $last_report = ServiceReport::where('store_id', $store_id)->latest('id')->first();
        $folio = $last_report ? intval($last_report->folio) + 1 : 1;

        // Ruta a la vista de Inertia (ej: 'ServiceReport/Create14.vue')
        $customViewPath = resource_path("js/Pages/ServiceReport/Create{$store_id}.vue");

        // Usar la vista personalizada si existe, sino la predeterminada
        $view = File::exists($customViewPath)
            ? "ServiceReport/Create{$store_id}"
            : "PageNotFound"; // 404 not found vista

        if (auth()->user()->store_id == 24 || auth()->user()->store_id == 25) {
            return inertia('ServiceReport/Create24', compact('products', 'folio'));
        }
        return inertia($view, compact('products', 'folio'));
        // return inertia('ServiceReport/Create24', compact('products', 'folio')); // Para hacer pruebas con la vista deseada
    }

    //para guardar la orden de dm compresores.
    public function store(Request $request)
    {
        $request->validate([
            'service_date' => 'required',
            'client_name' => 'required|string|max:255',
            'client_department' => 'required|string|max:255',
            'spare_parts' => 'nullable|array|min:1',
            'technician_name' => 'required|string|max:255',
            'receiver_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'service_cost' => 'nullable|numeric|min:0|max:999999',
        ]);

        $this->finalStepStore($request);

        return to_route('service-reports.index');
    }

    //para guardar ordenes de servicio de tiendas de reparacion de celulares (validacion diferente que el store para dm compresores)
    public function storePhoneStores(Request $request)
    {
        $request->validate([
            'service_date' => 'required',
            'client_name' => 'required|string|max:255',
            'client_phone_number' => 'required|string|max:10',
            'spare_parts' => 'nullable|array|min:0',
            'technician_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'service_description' => 'required|string|max:1000',
            'service_cost' => 'required|numeric|min:0|max:999999',
            'observations' => 'required',
        ]);

        $this->finalStepStore($request);
    }

    public function finalStepStore($request)
    {
        $storeId = auth()->user()->store_id;
        $last_report = ServiceReport::where('store_id', $storeId)->latest('id')->first();
        $folio = $last_report ? intval($last_report->folio) + 1 : 1;

        $service_order = ServiceReport::create($request->all() + [
            'store_id' => $storeId,
            'folio' => $folio,
        ]);

        // Subir y asociar las imagenes
        if ($request->media) {
            $service_order->addAllMediaFromRequest()->each(function ($fileAdder) {
                // Guarda el archivo en la colección y obtiene el modelo Media
                $media = $fileAdder->toMediaCollection();

                // Ahora sí puedes acceder a getPath()
                $path = $media->getPath();

                // Validar tamaño, entorno y compresiones disponibles
                if (
                    filesize($path) > 400 * 1024 &&
                    app()->environment() === 'production' &&
                    $this->tinifyService->totalCompressions() < 500
                ) {
                    $this->tinifyService->optimizeImage($path);
                } else {
                    // Otra lógica para imágenes pequeñas o fuera de producción
                }
            });
        }
    }

    public function show($encoded_report_id)
    {
        // Decodificar el ID
        $decoded_report = base64_decode($encoded_report_id);
        $report = ServiceReport::with('media')->findOrFail($decoded_report);
        $store_id = auth()->user()->store_id;

        // Ruta a la vista de Inertia (ej: 'ServiceReport/Show1.vue')
        $customViewPath = resource_path("js/Pages/ServiceReport/Show{$store_id}.vue");

        // Usar la vista personalizada si existe, sino la predeterminada
        $view = File::exists($customViewPath)
            ? "ServiceReport/Show{$store_id}"
            : "PageNotFound"; // 404 not found vista

        if (auth()->user()->store_id == 24 || auth()->user()->store_id == 25) {
            return inertia('ServiceReport/Show24', compact('report'));
        }
        return inertia($view, compact('report'));
        // return inertia("ServiceReport/Show24", compact('report')); // Para hacer pruebas con la vista deseada
    }

    public function edit($encoded_report_id)
    {
        // Decodificar el ID
        $decoded_report = base64_decode($encoded_report_id);
        $report = ServiceReport::with('media')->findOrFail($decoded_report);
        $store_id = auth()->user()->store_id;
        $products = Product::where('store_id', $store_id)->get(['id', 'name', 'code', 'description']);

        // Ruta a la vista de Inertia (ej: 'ServiceReport/Edit1.vue')
        $customViewPath = resource_path("js/Pages/ServiceReport/Edit{$store_id}.vue");

        // Usar la vista personalizada si existe, sino la predeterminada
        $view = File::exists($customViewPath)
            ? "ServiceReport/Edit{$store_id}"
            : "PageNotFound"; // 404 not found vista

        if (auth()->user()->store_id == 24 || auth()->user()->store_id == 25) {
            return inertia('ServiceReport/Edit24', compact('report', 'products'));
        }

        return inertia($view, compact('report', 'products'));
        // return inertia("ServiceReport/Edit24", compact('report', 'products')); // Para hacer pruebas con la vista deseada
    }

    public function update(Request $request, ServiceReport $serviceReport)
    {
        $request->validate([
            'service_date' => 'required',
            'client_name' => 'required|string|max:255',
            'client_department' => 'required|string|max:255',
            'spare_parts' => 'nullable|array|min:1',
            'technician_name' => 'required|string|max:255',
            'receiver_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'service_cost' => 'nullable|numeric|min:0|max:999999',
        ]);

        $serviceReport->update($request->all());

        return to_route('service-reports.index');
    }

    public function updatePhoneStores(Request $request, ServiceReport $service_order)
    {
        $request->validate([
            'service_date' => 'required',
            'client_name' => 'required|string|max:255',
            'client_phone_number' => 'required|string|max:10',
            'spare_parts' => 'nullable|array|min:0',
            'technician_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'service_description' => 'required|string|max:1000',
            'service_cost' => 'required|numeric|min:0|max:999999',
            'observations' => 'required',
        ]);

        $service_order->update($request->all());

        // Subir y asociar las imagenes
        if ($request->media) {
            $service_order->addAllMediaFromRequest()->each(fn($file) => $file->toMediaCollection());
        }

        return to_route('service-reports.index');
    }

    public function destroy(ServiceReport $service_report)
    {
        $service_report->delete();
    }

    public function searchServiceReport(Request $request)
    {
        $query = $request->input('query');

        $reports = ServiceReport::where('store_id', auth()->user()->store_id)
            ->where(function ($q) use ($query) {
                // Normaliza el query para comparar también sin ceros a la izquierda
                $queryNormalized = ltrim($query, '0');

                $q->where('client_name', 'like', "%$query%")
                    ->orWhere(DB::raw('CAST(folio AS CHAR)'), 'like', "%$query%")
                    ->orWhere(DB::raw('CAST(folio AS UNSIGNED)'), 'like', "%$queryNormalized%")
                    ->orWhere('service_date', 'like', "%$query%");
            })
            ->get();

        return response()->json(['items' => $reports]);
    }

    public function getDataForTable()
    {
        $perPage = request('pageSize', 100);
        $page = request('page', 1);

        $reports = ServiceReport::where('store_id', auth()->user()->store_id)
            ->latest('id')
            ->paginate($perPage, ['*'], 'page', $page);

        $data = [
            'reports' => $reports->items(),
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $reports->total(),
            ]
        ];

        return response()->json(compact('data'));
    }

    public function changeStatus(Request $request, ServiceReport $service_report)
    {
        $data = [
            'status' => $request->status,
        ];

        if ($request->status === 'Cancelada') {
            $aditionals = $service_report->aditionals ?? [];

            $aditionals['review_amount'] = $request->reviewAmount;
            $aditionals['advance_amount'] = $request->advanceAmount;

            $data['cancellation_reason'] = $request->cancellation_reason;
            $data['aditionals'] = $aditionals;
            $data['paid_at'] = now(); // Fecha y hora del pago
        } elseif ($request->status === 'Entregado/Pagado') {
            $data['payment_method'] = $request->paymentMethod;
            // $data['money_received'] = $request->money_received; // Dinero recibido al pagar la orden por si se quiere guardar en base de datos
            $data['paid_at'] = now(); // Fecha y hora del pago

            // crear gasto de comision del técnico si la comision es mayor a 0
            if ($service_report->comision_percentage > 0 && $service_report->service_cost > 0) {
                $expense = Expense::create([
                    'concept' => 'Comision de servicio técnico a ' . $service_report->technician_name,
                    'quantity' => 1,
                    'current_price' => ($service_report->comision_percentage / 100) * $service_report->service_cost,
                    'store_id' => $service_report->store_id,
                ]);

                // Registrar el movimiento de caja para el gasto de comision
                $cash_register = auth()->user()->cashRegister;
                CashRegisterMovement::create([
                    'amount' => ($service_report->comision_percentage / 100) * $service_report->service_cost,
                    'type' => 'Retiro',
                    'notes' => 'Registro. Comisión ' . $service_report->comision_percentage . '% ' . $service_report->technician_name . '. Venta folio ' . $service_report->folio,
                    'cash_register_id' => $cash_register->id,
                    'expense_id' => $expense->id,
                ]);
            }
        }

        $service_report->update($data);

        return response()->json(['report' => $service_report]);
    }

    public function massiveDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:service_reports,id',
        ]);

        try {
            ServiceReport::whereIn('id', $request->ids)->delete();

            return response()->json([
                'message' => 'Ordenes eliminadas correctamente.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ocurrió un error al eliminar las Ordenes.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // abre la plantilla de comprobante de servicio para imprimir de reparacion de celulares (apontephone)
    public function printTemplate(ServiceReport $report)
    {
        return inertia('ServiceReport/PrintTemplate24', compact('report'));
    }

    // carga los productos (refacciones) a la vista create o edit ara seleccionarlos (apontephone)
    public function fetchSpareParts()
    {
        $store_id = auth()->user()->store_id;
        $spare_parts = Product::where('store_id', $store_id)->get(['id', 'name', 'public_price', 'current_stock', 'min_stock', 'store_id']);

        return response()->json(['spare_parts' => $spare_parts]);
    }
}
