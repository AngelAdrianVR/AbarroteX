<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    
    public function index()
    {
        $services = Service::where('store_id', auth()->user()->store_id)->get()->take(30);
        $total_services = Service::where('store_id', auth()->user()->store_id)->get()->count();

        return inertia('Service/Index', compact('services', 'total_services'));
    }

    
    public function create()
    {
        return inertia('Service/Create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'descripton' => 'nullable|string|max:800',
            'price' => 'nullable|numeric|min:0|max:99999',
        ]);

        Service::create($request->all() + ['store_id' => auth()->user()->store_id]);

        return to_route('services.index');
    }

    
    public function show($encoded_service_id)
    {
        // Decodificar el ID
        $decoded_service = base64_decode($encoded_service_id);
        $service = Service::find($decoded_service);
        $services = Service::where('store_id', auth()->user()->store_id)->get(['id', 'name']);

        return inertia('Service/Show', compact('service', 'services'));
    }

    
    public function edit($encoded_service_id)
    {
        // Decodificar el ID
        $decoded_service = base64_decode($encoded_service_id);
        $service = Service::find($decoded_service);

        return inertia('Service/Edit', compact('service'));
    }

    
    public function update(Request $request, Service $service)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'descripton' => 'nullable|string|max:800',
            'price' => 'nullable|numeric|min:0|max:99999',
        ]);

        $service->update($request->all());

        return to_route('services.index');
    }


    public function destroy(Service $service)
    {
        $service->delete();
    }


    public function searchService(Request $request)
    {
        $query = $request->input('query');

        $services = Service::where('store_id', auth()->user()->store_id)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%$query%")
                    ->orWhere('category', 'like', "%$query%");
            })
            ->get();

        return response()->json(['items' => $services]);
    }


    public function getItemsByPage($currentPage)
    {
        $offset = $currentPage * 30;

        $services = Service::where('store_id', auth()->user()->store_id)->latest()->skip($offset)->take(30)->get();

        return response()->json(['items' => $services]);
    }


    public function fetchAllServices()
    {
        $services = Service::where('store_id', auth()->user()->store_id)->get();

        return response()->json(compact('services'));
    }


    
}
