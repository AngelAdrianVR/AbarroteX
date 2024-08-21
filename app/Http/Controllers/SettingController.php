<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SettingController extends Controller
{

    public function index()
    {
        $users = User::with(['cashRegister:id,name', 'roles'])
            ->where('store_id', auth()->user()->store_id)
            ->whereDoesntHave('roles', function ($query) {
                $query->where('name', 'Administrador');
            })
            ->get();

        return inertia('Setting/Index', compact('users'));
    }

    public function storeRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'permissions' => 'array|min:1'
        ]);

        $role = Role::create(['name' => $request->name, 'store_id' => auth()->user()->store_id]);
        $role->syncPermissions($request->permissions);
    }

    public function updateRole(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'array|min:1'
        ]);

        $role->name = $request->name;
        $role->save();
        $role->syncPermissions($request->permissions);
    }

    public function deleteRole(Role $role)
    {
        $role->delete();
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
