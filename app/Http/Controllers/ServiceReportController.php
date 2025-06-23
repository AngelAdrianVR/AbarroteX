<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ServiceReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ServiceReportController extends Controller
{
    public function index()
    {
        $service_reports = ServiceReport::latest('id')->where('store_id', auth()->user()->store_id)->get()->take(50);
        $total_reports = ServiceReport::where('store_id', auth()->user()->store_id)->get()->count();

        return inertia('ServiceReport/Index', compact('service_reports', 'total_reports'));
    }

    public function create()
    {
        $storeId = auth()->user()->store_id;
        $products = Product::where('store_id', auth()->user()->store_id)->get(['id', 'name', 'code', 'description']);
        $store_id = auth()->user()->store_id;
        $last_report = ServiceReport::where('store_id', $storeId)->latest('id')->first();
        $folio = $last_report ? intval($last_report->folio) + 1 : 1;

        // Ruta a la vista de Inertia (ej: 'ServiceReport/Create14.vue')
        $customViewPath = resource_path("js/Pages/ServiceReport/Create{$store_id}.vue");

        // Usar la vista personalizada si existe, sino la predeterminada
        $view = File::exists($customViewPath)
            ? "ServiceReport/Create{$store_id}"
            : "PageNotFound"; // 404 not found vista

        return inertia($view, compact('products', 'folio'));
    }

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

        $storeId = auth()->user()->store_id;
        $last_report = ServiceReport::where('store_id', $storeId)->latest('id')->first();
        $folio = $last_report ? intval($last_report->folio) + 1 : 1;

        ServiceReport::create($request->all() + [
            'store_id' => $storeId,
            'folio' => $folio,
        ]);

        return to_route('service-reports.index');
    }

    public function show($encoded_report_id)
    {
        // Decodificar el ID
        $decoded_report = base64_decode($encoded_report_id);
        $report = ServiceReport::findOrFail($decoded_report);

        return inertia('ServiceReport/Show', compact('report'));
    }

    public function edit($encoded_report_id)
    {
        // Decodificar el ID
        $decoded_report = base64_decode($encoded_report_id);
        $report = ServiceReport::findOrFail($decoded_report);
        $products = Product::where('store_id', auth()->user()->store_id)->get(['id', 'name', 'code', 'description']);

        return inertia('ServiceReport/Edit', compact('report', 'products'));
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

    public function destroy(ServiceReport $serviceReport)
    {
        //
    }

    public function searchServiceReport(Request $request)
    {
        $query = $request->input('query');

        $reports = ServiceReport::where('store_id', auth()->user()->store_id)
            ->where(function ($q) use ($query) {
                $q->where('client_name', 'like', "%$query%")
                    ->orWhere('folio', 'like', "%$query%")
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
        $service_report->update([
            'status' => $request->status
        ]);

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

}
