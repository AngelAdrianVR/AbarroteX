<?php

namespace App\Http\Controllers;

use App\Models\SupportReport;
use Illuminate\Http\Request;

class SupportReportController extends Controller
{

    public function index()
    {
        //
    }

    public function create()
    {
        return inertia('SupportReport/Create');
    }

    public function store(Request $request)
    {
        $valiidated = $request->validate([
            'type' => 'required|string|max:255',
            'description' => 'required|string|max:800',
            'first_report' => 'boolean',
        ]);

        $report = SupportReport::create($valiidated + ['user_id' => auth()->id(), 'store_id' => auth()->user()->store_id]);

        // archivos adjuntos
        $report->addAllMediaFromRequest()->each(fn ($file) => $file->toMediaCollection());

        return to_route('supports.index');
    }

    public function show(SupportReport $supportReport)
    {
        //
    }

    public function edit(SupportReport $supportReport)
    {
        //
    }

    public function update(Request $request, SupportReport $supportReport)
    {
        //
    }

    public function destroy(SupportReport $supportReport)
    {
        //
    }
}
