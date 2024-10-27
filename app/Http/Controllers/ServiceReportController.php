<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ServiceReport;
use Illuminate\Http\Request;

class ServiceReportController extends Controller
{
    public function index()
    {
        $service_reports = ServiceReport::where('store_id', auth()->user()->store_id)->get()->take(30);
        $total_reports = ServiceReport::where('store_id', auth()->user()->store_id)->get()->count();

        return inertia('ServiceReport/Index', compact('service_reports', 'total_reports'));
    }

    public function create()
    {
        $products = Product::where('store_id', auth()->user()->store_id)->get(['id', 'name', 'code', 'description']);

        return inertia('ServiceReport/Create', compact('products'));
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

    public function edit(ServiceReport $serviceReport)
    {
        //
    }

    public function update(Request $request, ServiceReport $serviceReport)
    {
        //
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
                    ->orWhere('receiver_name', 'like', "%$query%")
                    ->orWhere('technician_name', 'like', "%$query%");
            })
            ->get();

        return response()->json(['items' => $reports]);
    }

    public function getItemsByPage($currentPage)
    {
        $offset = $currentPage * 30;

        $reports = ServiceReport::where('store_id', auth()->user()->store_id)->latest()->skip($offset)->take(30)->get();

        return response()->json(['items' => $reports]);
    }
}
