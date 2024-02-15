<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index() 
    {
        $sales = Sale::with('product')->whereDate('created_at', today())->get();

        return inertia('Dashboard', compact('sales'));
    }
}
