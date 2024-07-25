<?php

namespace App\Http\Controllers;

use App\Models\Size;
use Illuminate\Http\Request;

class SizeController extends Controller
{
    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'short' => 'nullable|string|max:255',
            'category' => 'required|string|max:255',
        ]);

        $size = Size::create($validated);

        return response()->json(['item' => $size]);
    }

    public function show(Size $size)
    {
        //
    }

    public function edit(Size $size)
    {
        //
    }

    public function update(Request $request, Size $size)
    {
        //
    }

    public function destroy(Size $size)
    {
        //
    }
}
