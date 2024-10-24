<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EzyProfileController extends Controller
{
    public function updateBasic(Request $request)
    {
        $request->validate([
            'store_name' => 'required|string|max:255',
            'store_address' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'contact_phone' => 'nullable|string|max:13',
            'email' => 'required|string|max:255|unique:users,email,'.auth()->id(),
        ]);

        $user = auth()->user();
        $user->update([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('contact_phone'),
        ]);
        $user->store->update([
            'name' => $request->input('store_name'),
            'address' => $request->input('store_address'),
            // 'contact_phone' => $request->input('contact_phone'),
            'contact_name' => $request->input('name'),
        ]);
    }
    
    public function updateSuscription(Request $request)
    {
        $validated = $request->validate([
            'suscription_period' => 'required|string|max:255',
            'default_card_id' => 'nullable|numeric|min:1',
        ]);

        $store = auth()->user()->store;
        $store->update($validated);
    }
}
