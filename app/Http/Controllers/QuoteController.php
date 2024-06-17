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
        //
    }

    
    public function show(Quote $quote)
    {
        //
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
