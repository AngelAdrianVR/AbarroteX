<?php

namespace App\Http\Controllers;

use App\Models\DiscountTicket;
use App\Models\Partner;
use Illuminate\Http\Request;

class DiscountTicketController extends Controller
{
    public function index()
    {
        //
    }
    
    public function referralsIndex()
    {
        $partner = Partner::with(['discountTicket'])->firstWhere('name', auth()->user()->store->name);
        
        return inertia('Referrals/Index', compact('partner'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(DiscountTicket $discountTicket)
    {
        //
    }

    public function edit(DiscountTicket $discountTicket)
    {
        //
    }

    public function update(Request $request, DiscountTicket $discountTicket)
    {
        //
    }

    public function destroy(DiscountTicket $discountTicket)
    {
        //
    }

    public function fetchActiveTickets()
    {
        $discount_tickets = DiscountTicket::where('is_active', true)->get();

        return response()->json(compact('discount_tickets'));
    }
}
