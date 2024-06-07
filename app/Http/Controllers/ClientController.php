<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    
    public function index()
    {
        $clients = Client::where('store_id', auth()->user()->store_id)->latest()->get()->take(20);
        $total_clients = Client::where('store_id', auth()->user()->store_id)->get()->count();

        return inertia('Client/Index', compact('clients', 'total_clients'));
    }

    
    public function create()
    {
        return inertia('Client/Create');
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|min:10|max:10',
            'notes' => 'nullable|string|max:255',
            'street' => $request->addAddress ? 'required|string|max:255' : 'nullable',
            'suburb' => $request->addAddress ? 'required|string|max:255' : 'nullable',
            'ext_number' => $request->addAddress ? 'required|string|max:8' : 'nullable',
            'int_number' => 'nullable|string|max:8',
            'postal_code' => $request->addAddress ? 'required|string|max:6' : 'nullable',
            'town' => $request->addAddress ? 'required|string|max:100' : 'nullable',
            'polity_state' => $request->addAddress ? 'required|string|max:100' : 'nullable',
            'razon_social' => $request->addFiscalInfo ? 'required|string|max:100' : 'nullable',
            'rfc' => $request->addFiscalInfo ? 'required|string|max:100' : 'nullable',
            'tax_regime' => $request->addFiscalInfo ? 'required|string|max:100' : 'nullable',
        ]);

        Client::create($request->all() + ['store_id' => auth()->user()->store_id]);

        return to_route('clients.index');
    }

    
    public function show(Client $client)
    {
        $clients = Client::where('store_id', auth()->user()->store_id)->latest()->get(['id', 'name']);

        return inertia('Client/Show', compact('client', 'clients'));
    }

    
    public function edit(Client $client)
    {
        return inertia('Client/Edit', compact('client'));
    }

    
    public function update(Request $request, Client $client)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|min:10|max:10',
            'notes' => 'nullable|string|max:255',
            'street' => $request->addAddress ? 'required|string|max:255' : 'nullable',
            'suburb' => $request->addAddress ? 'required|string|max:255' : 'nullable',
            'ext_number' => $request->addAddress ? 'required|string|max:8' : 'nullable',
            'int_number' => 'nullable|string|max:8',
            'postal_code' => $request->addAddress ? 'required|string|max:6' : 'nullable',
            'town' => $request->addAddress ? 'required|string|max:100' : 'nullable',
            'polity_state' => $request->addAddress ? 'required|string|max:100' : 'nullable',
            'razon_social' => $request->addFiscalInfo ? 'required|string|max:100' : 'nullable',
            'rfc' => $request->addFiscalInfo ? 'required|string|max:100' : 'nullable',
            'tax_regime' => $request->addFiscalInfo ? 'required|string|max:100' : 'nullable',
        ]);

        $client->update($request->all());

        return to_route('clients.index');
    }

    
    public function destroy(Client $client)
    {
        $client->delete();
    }


    public function getItemsByPage($currentPage)
    {
        $offset = $currentPage * 20;

        $clients = Client::where('store_id', auth()->user()->store_id)->latest()->skip($offset)->take(20)->get();

        return response()->json(['items' => $clients]);
    }


    public function searchClient(Request $request)
    {
        $query = $request->input('query');

        // Realiza la búsqueda en la base de datos local
        $clients = Client::where('store_id', auth()->user()->store_id)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%$query%")
                    ->orWhere('phone', 'like', "%$query%")
                    ->orWhere('rfc', 'like', "%$query%");
            })
            ->get();

        return response()->json(['items' => $clients]);
    }


    public function printHistorial(Client $client)
    {
        return inertia('Client/PrintHistorial');
    }
}
