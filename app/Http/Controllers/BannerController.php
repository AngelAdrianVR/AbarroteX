<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
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

   
    public function show(Banner $banner)
    {
        //
    }

    
    public function edit(Banner $banner)
    {
        //
    }

    
    public function update(Request $request, Banner $banner)
    {
        //
    }


    public function updateWithMedia(Request $request, Banner $banner)
    {
        // Eliminar imágenes antiguas solo si se borró desde el input y no se agregó una nueva
        if ($request->clearedBanner1) {
            $banner->clearMediaCollection('banner1');
        }

        if ($request->clearedBanner2) {
            $banner->clearMediaCollection('banner2');
        }

        if ($request->clearedBanner3) {
            $banner->clearMediaCollection('banner3');
        }

        if ($request->clearedLogo) {
            $banner->clearMediaCollection('logo');
        }

        // Eliminar imágenes antiguas solo si se proporcionan nuevas imágenes
        if ($request->hasFile('banner1')) {
            $banner->clearMediaCollection('banner1');
        }

        if ($request->hasFile('banner2')) {
            $banner->clearMediaCollection('banner2');
        }

        if ($request->hasFile('banner3')) {
            $banner->clearMediaCollection('banner3');
        }

        if ($request->hasFile('logo')) {
            $banner->clearMediaCollection('logo');
        }

        // Guardar el archivo en la colección 'banner1'
        if ($request->hasFile('banner1')) {
            $banner->addMediaFromRequest('banner1')->toMediaCollection('banner1');
        }

        // Guardar el archivo en la colección 'banner2'
        if ($request->hasFile('banner2')) {
            $banner->addMediaFromRequest('banner2')->toMediaCollection('banner2');
        }

        // Guardar el archivo en la colección 'banner3'
        if ($request->hasFile('banner3')) {
            $banner->addMediaFromRequest('banner3')->toMediaCollection('banner3');
        }

        // Guardar el archivo en la colección 'logo'
        if ($request->hasFile('logo')) {
            $banner->addMediaFromRequest('logo')->toMediaCollection('logo');
        }

    }

    
    public function destroy(Banner $banner)
    {
        //
    }
}
