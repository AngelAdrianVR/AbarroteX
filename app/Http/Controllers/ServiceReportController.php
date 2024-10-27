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
        //
    }

    public function show(ServiceReport $serviceReport)
    {
        //
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
