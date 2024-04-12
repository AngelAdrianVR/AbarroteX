<?php

namespace App\Http\Controllers;

use App\Models\Card;
use Illuminate\Http\Request;

class CardController extends Controller
{
    
    public function index()
    {
        //
    }

    public function create()
    {
        return inertia('Card/Create');
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Card $card)
    {
        //
    }

    public function edit(Card $card)
    {
        //
    }

    public function update(Request $request, Card $card)
    {
        //
    }

    public function destroy(Card $card)
    {
        //
    }
}
