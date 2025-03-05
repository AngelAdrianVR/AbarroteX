<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    public function index()
    {
        //
    }

    public function create()
    {
        //
    }


    public function landingCreate()
    {
        return inertia('PartnerRegister');
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Partner $partner)
    {
        //
    }

    public function edit(Partner $partner)
    {
        //
    }

    public function update(Request $request, Partner $partner)
    {
        //
    }

    public function destroy(Partner $partner)
    {
        //
    }
}
