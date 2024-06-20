<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Quote;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    
    public function index()
    {
        $quotes = Quote::where('store_id', auth()->user()->store_id)->latest()->get()->take(20);
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

        $quote = Quote::with(['client:id,name'])
            ->findOrFail($decoded_quote_id);

        return inertia('Quote/Show', compact('quote'));
    }

    
    public function edit($encoded_quote_id)
    {
        $clients = Client::where('store_id', auth()->user()->store_id)->get(['id', 'name']);

        // Decodificar el ID
        $decoded_quote_id = base64_decode($encoded_quote_id);

        $quote = Quote::with(['client:id,name'])
            ->findOrFail($decoded_quote_id);

        return inertia('Quote/Edit', compact('quote', 'clients'));
    }

    
    public function update(Request $request, Quote $quote)
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

        $quote->update($request->all());

        //codifica el id del quote
        $encoded_quote_id = base64_encode($quote->id);

        // return to_route('quotes.show', ['quote' => $encoded_quote_id]); //para mandar al show
        return to_route('quotes.index');
    }

    
    public function destroy(Quote $quote)
    {
        $quote->delete();
    }


    public function searchQuote(Request $request)
    {
        $query = $request->input('query');

        $quotes = Quote::where('store_id', auth()->user()->store_id)
            ->where(function ($q) use ($query) {
                $q->where('contact_name', 'like', "%$query%")
                    ->orWhere('id', 'like', "%$query%");
            })
            ->get();

        return response()->json(['items' => $quotes]);
    }


    public function getItemsByPage($currentPage)
    {
        $offset = $currentPage * 20;

        $quotes = Quote::where('store_id', auth()->user()->store_id)->latest()->skip($offset)->take(20)->get();

        return response()->json(['items' => $quotes]);
    }
}
