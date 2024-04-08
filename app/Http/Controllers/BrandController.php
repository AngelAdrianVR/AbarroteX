<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
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
        $request->validate([
            'name' =>'required|string|max:100|unique:brands,name',
        ]);

        $brand = Brand::create($request->all());

        return response()->json(['item' => $brand]);
    }

    
    public function show(Brand $brand)
    {
        //
    }

    
    public function edit(Brand $brand)
    {
        //
    }

    
    public function update(Request $request, Brand $brand)
    {
        //
    }

    
    public function destroy(Brand $brand)
    {
        //
    }
}
