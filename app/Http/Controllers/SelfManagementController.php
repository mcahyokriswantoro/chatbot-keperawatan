<?php

namespace App\Http\Controllers;

use App\Models\ScreeningSession;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SelfManagementController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $hasScreening = $user->screeningSessions()->exists();
        $latestScreening = $user->screeningSessions()->latest()->first();

        $diseases = collect(config('self_management_diseases.list'))
            ->map(function (array $entry, string $key) use ($user) {
                $diseaseConfig = config("diseases.{$key}", []);

                $latestScreening = $user
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
                    'has_screening' => $latestScreening !== null,
                ];
            })
            ->values();

        return view('self-management.index', compact('diseases', 'hasScreening', 'latestScreening'));
    }

    public function show(string $disease, Request $request): View
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

        $hasScreening = $latestScreening instanceof ScreeningSession;

        $recommendedRisk = $request->query('risk');
        if (! in_array($recommendedRisk, config('self_management_diseases.risk_levels'), true)) {
            $recommendedRisk = $latestScreening?->selfManagementRiskKey();
        }

        return view('self-management.show', [
            'disease' => $disease,
            'label' => $diseaseConfig['label'] ?? $disease,
            'icon' => $diseaseConfig['icon'] ?? '📋',
            'description' => $diseaseConfig['description'] ?? '',
            'guide' => $guide,
            'recommendedRisk' => $hasScreening ? $recommendedRisk : null,
            'latestScreening' => $latestScreening,
            'hasScreening' => $hasScreening,
            'userGender' => auth()->user()->gender,
        ]);
    }
}
