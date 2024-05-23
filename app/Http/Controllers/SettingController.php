<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Logo;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    
    public function index()
    {
        $users = User::with(['cashRegister:id,name'])
            ->where('store_id', auth()->user()->store_id)
            ->where('rol', '!=', 'Administrador')
            ->get();

        $banners = Banner::with(['media'])->where('store_id', auth()->user()->store_id)->first();
        $logo = Logo::with(['media'])->where('store_id', auth()->user()->store_id)->first();
        
        // return $logo;
        return inertia('Setting/Index', compact('users', 'banners', 'logo'));
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
