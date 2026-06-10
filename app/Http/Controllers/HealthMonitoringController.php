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
            'complaints' => ['nullable', 'string', 'max:2000'],
            'medication_name' => ['nullable', 'string', 'max:255'],
            'medication_dose' => ['nullable', 'string', 'max:255'],
            'medication_schedule' => ['nullable', 'string', 'max:255'],
            'activities' => ['nullable', 'string', 'max:2000'],
            'diet_compliant' => ['nullable', 'in:ya,tidak'],
            'diet_notes' => ['nullable', 'string', 'max:2000'],
            'systolic' => ['nullable', 'integer', 'min:50', 'max:300'],
            'diastolic' => ['nullable', 'integer', 'min:30', 'max:200'],
            'heart_rate' => ['nullable', 'integer', 'min:30', 'max:250'],
            'temperature' => ['nullable', 'numeric', 'min:30', 'max:45'],
            'respiratory_rate' => ['nullable', 'integer', 'min:5', 'max:80'],
            'blood_sugar' => ['nullable', 'numeric', 'min:0', 'max:600'],
            'oxygen_saturation' => ['nullable', 'integer', 'min:50', 'max:100'],
            'weight' => ['nullable', 'numeric', 'min:0', 'max:500'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'recorded_at' => ['required', 'date'],
        ]);

        if (isset($validated['diet_compliant'])) {
            $validated['diet_compliant'] = $validated['diet_compliant'] === 'ya';
        }

        auth()->user()->healthMonitorings()->create($validated);

        return back()->with('status', 'Data monitoring self management berhasil disimpan.');
    }
}
