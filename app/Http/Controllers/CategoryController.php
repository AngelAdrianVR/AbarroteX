<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
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
            'name' =>'required|string|max:100|unique:categories,name',
        ]);

        // guardar business_line_name con el id de la tienda para que solo ella pueda verlo
        $category = Category::create($request->all() + ['business_line_name' => auth()->user()->store->id]);

        return response()->json(['item' => $category]);
    }

    
    public function show(Category $category)
    {
        //
    }

    
    public function edit(Category $category)
    {
        //
    }

    
    public function update(Request $request, Category $category)
    {
        //
    }

    
    public function destroy(Category $category)
    {
        //
    }
}
