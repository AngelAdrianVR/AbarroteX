<?php

namespace App\Http\Controllers;

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
    
    public function storeLogo(Request $request)
    {
        $store = auth()->user()->store;

        // borrar logo anterior
        $store->clearMediaCollection('logo');

        // guardar nuevo logo
        $store->addAllMediaFromRequest()->each(fn($file) => $file->toMediaCollection('logo'));
    }

    public function storeTicketLogo(Request $request)
    {
        $store = auth()->user()->store;

        // borrar logo anterior
        $store->clearMediaCollection('ticketLogo');

        // guardar nuevo logo
        $store->addAllMediaFromRequest()->each(fn($file) => $file->toMediaCollection('ticketLogo'));
    }
    
    public function storeBanner(Request $request)
    {
        $store = auth()->user()->store;

        // borrar banner anterior
        $store->clearMediaCollection('banner');

        if ($request->has('img')) {
            // guardar nuevo banner
            $store->addAllMediaFromRequest()->each(fn($file) => $file->toMediaCollection('banner'));
        }

        // guardar banner por defecto seleccionado o 0 si elige imagen personalizada
        $osp = $store->online_store_properties;
        $osp['banner'] = $request->selected;
        $store->update(['online_store_properties' => $osp]);
    }
    
    public function storeOnlineProperties(Request $request)
    {
        $store = auth()->user()->store;

        $validated = $request->validate([
            'whatsapp' => 'nullable|string|min:10|max:10',
            'delivery_price' => 'nullable|numeric|min:0|max:9999999',
            'delivery_conditions' => 'nullable|string|max:500',
            'min_free_delivery' => 'nullable|numeric|min:0|max:9999999',
            'inventory' => 'boolean',
            'sold_out_active' => 'boolean',
        ]);

        // guardar otros datos de la tienda en linea para que no se borren
        $validated['banner'] = key_exists('banner', $store->online_store_properties)
        ? $store->online_store_properties['banner']
        : 1;
        $validated['cash_payment'] = key_exists('cash_payment', $store->online_store_properties)
        ? $store->online_store_properties['cash_payment']
        : true;
        $validated['card_payment'] = key_exists('card_payment', $store->online_store_properties)
        ? $store->online_store_properties['card_payment']
        : false;
        $validated['mercado_pago'] = key_exists('mercado_pago', $store->online_store_properties)
        ? $store->online_store_properties['mercado_pago']
        : false;

        // guardar datos
        $store->update(['online_store_properties' => $validated]);
    }

    public function toggleSettingValue(Request $request, Store $store, $setting_id)
    {
        $new_value = $request->value ? 1 : null;
        $store->settings()->updateExistingPivot($setting_id, ['value' => $new_value]);

        return response()->json([]);
    }

    //modifica los módulos activados para la suscripción cuando no se ha realizado el pago
    //para despues pagar los activos.
    public function updateModules(Request $request, Store $store)
    {   
        $store->update([
            'activated_modules' => $request->activated_modules
        ]);        
    }
    
    public function fetchStoreInfo(Store $store)
    {
        $store->load('media');
        return response()->json(compact('store'));
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'store_id' => 'required|exists:stores,id',
        ]);

        $store = Store::with('settings')->findOrFail($validated['store_id']);

        // IDs que deben tener valor null
        $falseIds = [3, 7, 8];

        foreach ($store->settings as $setting) {
            $value = in_array($setting->id, $falseIds) ? null : 1;

            $store->settings()->updateExistingPivot($setting->id, [
                'value' => $value
            ]);
        }

        return response()->json(['message' => 'Configuraciones actualizadas forzadamente.'], 200);
    }




}
