<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class SelfManagementController extends Controller
{
    public function index(): View
    {
        $diseases = collect(config('self_management_diseases.list'))
            ->map(function (array $entry, string $key) {
                $diseaseConfig = config("diseases.{$key}", []);

                $latestScreening = auth()->user()
                    ->screeningSessions()
                    ->where('disease', $key)
                    ->latest()
                    ->first();

                return [
                    'key' => $key,
                    'label' => $diseaseConfig['label'] ?? $key,
                    'icon' => $diseaseConfig['icon'] ?? '📋',
                    'description' => $diseaseConfig['description'] ?? '',
                    'latest_risk' => $latestScreening?->displayRiskLabel(),
                ];
            })
            ->values();

        return view('self-management.index', compact('diseases'));
    }

    public function show(string $disease, \Illuminate\Http\Request $request): View
    {
        $registry = config('self_management_diseases.list');
        abort_unless(isset($registry[$disease]), 404);

        $diseaseConfig = config("diseases.{$disease}", []);
        $guide = config($registry[$disease]['config']);

        $latestScreening = auth()->user()
            ->screeningSessions()
            ->where('disease', $disease)
            ->latest()
            ->first();

        $recommendedRisk = $request->query('risk');
        if (! in_array($recommendedRisk, config('self_management_diseases.risk_levels'), true)) {
            $recommendedRisk = $latestScreening?->displayRiskLabel();
        }

        return view('self-management.show', [
            'disease' => $disease,
            'label' => $diseaseConfig['label'] ?? $disease,
            'icon' => $diseaseConfig['icon'] ?? '📋',
            'guide' => $guide,
            'recommendedRisk' => $recommendedRisk,
            'latestScreening' => $latestScreening,
        ]);
    }
}
