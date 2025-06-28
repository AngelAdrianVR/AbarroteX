<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Service;
use App\Models\ServiceReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use App\Services\TinifyService;

class ServiceReportController extends Controller
{
    public function __construct(protected TinifyService $tinifyService) {}

    public function index()
    {
        $service_reports = ServiceReport::latest('id')->where('store_id', auth()->user()->store_id)->get()->take(50);
        $total_reports = ServiceReport::where('store_id', auth()->user()->store_id)->get()->count();

        return inertia('ServiceReport/Index', compact('service_reports', 'total_reports'));
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

        return inertia($view, compact('products', 'folio'));
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
            'spare_parts' => 'nullable|array|min:1',
            'technician_name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'service_description' => 'required|string|max:1000',
            'service_cost' => 'required|numeric|min:0|max:999999',
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
        if ( $request->media ) {
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

        return inertia($view, compact('report'));
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

        return inertia($view, compact('report', 'products'));
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
            'spare_parts' => 'nullable|array|min:1',
            'technician_name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'service_description' => 'required|string|max:1000',
            'service_cost' => 'required|numeric|min:0|max:999999',
        ]);

        $service_order->update($request->all());

        // Subir y asociar las imagenes
        if ( $request->media ) {
            $service_order->addAllMediaFromRequest()->each(fn ($file) => $file->toMediaCollection());
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

    public function getItemsByPage($currentPage)
    {
        $offset = $currentPage * 50;

        $reports = ServiceReport::where('store_id', auth()->user()->store_id)->latest()->skip($offset)->take(50)->get();

        return response()->json(['items' => $reports]);
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
        } elseif ($request->status === 'Entregado/Pagado') {
            $data['payment_method'] = $request->paymentMethod;
            // $data['money_received'] = $request->money_received; // Dinero recibido al pagar la orden
            $data['paid_at'] = now(); // Fecha y hora del pago
        }

        $service_report->update($data);
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
        return inertia('ServiceReport/PrintTemplate1', compact('report'));
    }

}
