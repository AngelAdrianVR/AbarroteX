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

    public function storeCSF(Request $request)
    {
        $store = auth()->user()->store;

        // borrar constancia anterior si es que la hay
        if ($store->getFirstMedia('csf')) {
            $store->clearMediaCollection('csf');
        }

        $store->addAllMediaFromRequest()->each(fn($file) => $file->toMediaCollection('csf'));
    }

    public function toggleSettingValue(Request $request, Store $store, $setting_id)
    {
        $new_value = $request->value ? 1 : null;
        $store->settings()->updateExistingPivot($setting_id, ['value' => $new_value]);

        return response()->json([]);
    }

    public function updateOnlineSalesInfo(Request $request, Store $store)
    {
        $request->validate([
            'online_store_properties.whatsapp' => 'nullable|string|min:10|max:10',
            'online_store_properties.cash_payment' => 'nullable|boolean',
            'online_store_properties.credit_payment' => 'nullable|boolean',
            'online_store_properties.debit_payment' => 'nullable|boolean',
            'online_store_properties.mercado_pago' => 'nullable|boolean',
            'online_store_properties.delivery_price' => 'nullable|numeric|min:0|max:9999',
            'online_store_properties.delivery_conditions' => 'nullable|string|max:500',
            'online_store_properties.min_free_delivery' => 'nullable|numeric|min:0|max:9999',
        ]);

        $store->update($request->all());

        return to_route('online-sales.index', ['tab' => 2]);
    }

    // public function updatePrinterConfig(Request $request, Store $store)
    // {
    //     $request->validate([
    //         'printer_config.UUIDService' => 'nullable|string|min:1|max:255',
    //         'printer_config.UUIDCharacteristic' => 'nullable|string|min:1|max:255',
    //     ]);

    //     $store->update($request->all());
    // }

    public function fetchStoreInfo(Store $store)
    {
        return response()->json(compact('store'));
    }

}
