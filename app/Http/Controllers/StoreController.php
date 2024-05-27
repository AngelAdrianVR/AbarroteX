<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\SettingHistory;
use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
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
        //
    }

    public function show(Store $store)
    {
        //
    }

    public function edit(Store $store)
    {
        //
    }

    public function update(Request $request, Store $store)
    {
        //
    }

    public function destroy(Store $store)
    {
        //
    }

    // API
    public function getSettingsByModule(Store $store, $module)
    {
        $items = $store->settings()->where('module', $module)->get();

        return response()->json(compact('items'));
    }

    public function toggleSettingValue(Request $request, Store $store, $setting_id)
    {
        $new_value = $request->value ? 1 : null;
        $store->settings()->updateExistingPivot($setting_id, ['value' => $new_value]);
        $setting_name = Setting::find($setting_id)->key;
        $action = $new_value ? 'activó' : 'desactivó';

        // Guardar el movimiento en historial
        SettingHistory::create([
            'description' => $action . " la configuración \"$setting_name\"",
            'user_name' => auth()->user()->name,
        ]);

        return response()->json([]);
    }


    public function updateOnlineSalesInfo(Request $request, Store $store)
    {
        $request->validate([
            'whatsapp' => 'nullable|string|min:10|max:10',
            'cash_payment' => 'nullable|boolean',
            'credit_payment' => 'nullable|boolean',
            'debit_payment' => 'nullable|boolean',
            'mercado_pago' => 'nullable|boolean',
            'delivery_price' => 'nullable|numeric|min:0|max:9999',
            'delivery_conditions' => 'nullable|string|max:500',
            'min_free_delivery' => 'nullable|numeric|min:0|max:9999',
        ]);

        $store->update($request->all());
    }
}
