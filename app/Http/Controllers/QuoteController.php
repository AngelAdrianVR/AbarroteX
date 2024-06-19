<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Quote;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    
    public function index()
    {
        $quotes = Quote::where('store_id', auth()->user()->store_id)->get()->take(30);
        $total_quotes = Quote::where('store_id', auth()->user()->store_id)->get()->count();

        return inertia('Quote/Index', compact('quotes', 'total_quotes'));
    }

    
    public function create()
    {   
        $clients = Client::where('store_id', auth()->user()->store_id)->get(['id', 'name']);

        return inertia('Quote/Create', compact('clients'));
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'contact_name' => 'required|string|max:150',
            'phone' => 'required|string|min:10|max:10',
            'email' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:255',
            'show_iva' => 'boolean',
            'has_discount' => 'boolean',
            'discount' => 'nullable|numeric|min:0|max:99999',
            'is_percentage_discount' => 'boolean',
            'total' => 'required|numeric|min:0|max:999999',
            'products' => 'nullable',
            'services' => 'nullable',
            'client_id' => 'nullable',
        ]);

        Quote::create($request->all() + ['store_id' => auth()->user()->store_id]);

        return to_route('quotes.index');
    }

    
    public function show($encoded_quote_id)
    {
        // Decodificar el ID
        $decoded_quote_id = base64_decode($encoded_quote_id);

        $quote = Quote::with(['client'])
            ->findOrFail($decoded_quote_id);

        return inertia('Quote/Show', compact('quote'));
    }

    
    public function edit(Quote $quote)
    {
        //
    }

    
    public function update(Request $request, Quote $quote)
    {
        //
    }

    
    public function destroy(Quote $quote)
    {
        //
    }
}
