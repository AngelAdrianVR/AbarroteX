<?php

namespace App\Http\Controllers;

use App\Models\Tutorial;
use Illuminate\Http\Request;

class TutorialController extends Controller
{
    
    public function index()
    {   
        $videos = Tutorial::where('status', true)->get();

        return inertia('Tutorial/Index', compact('videos'));
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

    public function incrementViews(Tutorial $tutorial)
    {
        $tutorial->increment('views');
        return response()->json(['views' => $tutorial->views]);
    }


}
