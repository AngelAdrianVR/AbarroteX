<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Tutorial;
use Illuminate\Http\Request;

class TutorialController extends Controller
{
    
    public function index()
    {   
        return inertia('Tutorial/Index');
    }

    
    public function create()
    {
        //
    }

    
    public function store(Request $request)
    {
        //
    }

    
    public function show(Tutorial $tutorial)
    {
        //
    }

    
    public function edit(Tutorial $tutorial)
    {
        //
    }

    
    public function update(Request $request, Tutorial $tutorial)
    {

    }

    
    public function destroy(Tutorial $tutorial)
    {
        //
    }

}
