<?php

namespace App\Http\Controllers;

use App\Models\Color;
use Illuminate\Http\Request;

class ColorController extends Controller
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
            'list' => 'required|array',
            'list.*.name' => 'required|string|max:100',
            'list.*.color' => 'required',
        ], [
            'list.*.name.required' => 'El nombre es obligatorio.',
            'list.*.name.string' => 'El nombre debe ser una cadena de texto.',
            'list.*.name.max' => 'El nombre no debe exceder los 100 caracteres.',
            'list.*.color.required' => 'Campo obligatorio.',
        ]);

        $store_id = auth()->user()->store_id;
        foreach ($validated['list'] as $color) {
            Color::create($color + ['store_id' => $store_id]);
        }
    }

    public function show(Color $color)
    {
        //
    }

    public function edit(Color $color)
    {
        //
    }

    public function update(Request $request, Color $color)
    {
        //
    }

    public function destroy(Color $color)
    {
        //
    }
}
