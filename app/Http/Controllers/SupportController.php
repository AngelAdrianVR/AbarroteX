<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function index()
    {
        return inertia('Support/Index');
    }

    public function faqs()
    {
        return inertia('Support/Faqs');
    }

    public function suscription()
    {
        return inertia('Support/Suscription');
    }
}
