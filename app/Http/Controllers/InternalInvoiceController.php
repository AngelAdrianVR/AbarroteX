<?php

namespace App\Http\Controllers;

use App\Models\InternalInvoice;
use Illuminate\Http\Request;

class InternalInvoiceController extends Controller
{
    
    public function index()
    {
        $internal_invoices = InternalInvoice::where('store_id', auth()->user()->store_id)->get();

        return inertia('InternalInvoice/Index', compact('internal_invoices'));
    }

    
    public function create()
    {
        //
    }

    
    public function store(Request $request)
    {
        //
    }

    
    public function show(InternalInvoice $internalInvoice)
    {
        //
    }

    
    public function edit(InternalInvoice $internalInvoice)
    {
        //
    }

    
    public function update(Request $request, InternalInvoice $internalInvoice)
    {
        //
    }

    
    public function destroy(InternalInvoice $internalInvoice)
    {
        //
    }
}
