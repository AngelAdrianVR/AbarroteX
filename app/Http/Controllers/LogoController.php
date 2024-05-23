<?php

namespace App\Http\Controllers;

use App\Models\Logo;
use Illuminate\Http\Request;

class LogoController extends Controller
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


    public function show(Logo $logo)
    {
        //
    }

    
    public function edit(Logo $logo)
    {
        //
    }


    public function update(Request $request, Logo $logo)
    {
        //
    }

    public function updateWithMedia(Request $request, Logo $logo)
    {
        // Eliminar imágenes antiguas solo si se borró desde el input y no se agregó una nueva
        if ($request->clearedLogo) {
            $logo->clearMediaCollection('logo');
        }

        // Eliminar imágenes antiguas solo si se proporcionan nuevas imágenes
        if ($request->hasFile('logo')) {
            $logo->clearMediaCollection('logo');
        }

        // Guardar el archivo en la colección 'logo'
        if ($request->hasFile('logo')) {
            $logo->addMediaFromRequest('logo')->toMediaCollection('logo');
        }

    }


    public function destroy(Logo $logo)
    {
        //
    }
}
