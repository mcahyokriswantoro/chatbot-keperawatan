<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HealthMonitoringController extends Controller
{
    public function index(): View
    {
        $records = auth()->user()
            ->healthMonitorings()
            ->latest('recorded_at')
            ->paginate(10);

        return view('monitoring.index', compact('records'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'systolic' => ['nullable', 'integer', 'min:50', 'max:300'],
            'diastolic' => ['nullable', 'integer', 'min:30', 'max:200'],
            'heart_rate' => ['nullable', 'integer', 'min:30', 'max:250'],
            'blood_sugar' => ['nullable', 'numeric', 'min:0', 'max:600'],
            'weight' => ['nullable', 'numeric', 'min:0', 'max:500'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'recorded_at' => ['required', 'date'],
        ]);

        auth()->user()->healthMonitorings()->create($validated);

        return back()->with('status', 'Data monitoring berhasil disimpan.');
    }
}
