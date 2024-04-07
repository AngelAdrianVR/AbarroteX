<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    
    public function index()
    {
        // return Setting::first()->authStore;
        return inertia('Setting/Index');
    }

    
    public function create()
    {
        //
    }

    
    public function store(Request $request)
    {
        //
    }

    
    public function show(Setting $setting)
    {
        //
    }

    
    public function edit(Setting $setting)
    {
        //
    }

    
    public function update(Request $request, Setting $setting)
    {
        //
    }

    
    public function destroy(Setting $setting)
    {
        //
    }

    // API
    public function getByModule($module)
    {
        $items = Setting::where('module', $module)->get();

        return response()->json(compact('items'));
    }
}
