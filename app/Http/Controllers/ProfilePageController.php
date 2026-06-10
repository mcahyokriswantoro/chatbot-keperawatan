<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ProfilePageController extends Controller
{
    public function index(): View
    {
        $stats = null;

        if (auth()->check()) {
            $user = auth()->user();
            $bmi = null;
            $bmiLabel = null;
            $bmiTone = 'text-slate-600';

            if ($user->weight && $user->height && (float) $user->height > 0) {
                $heightM = (float) $user->height / 100;
                $bmi = round((float) $user->weight / ($heightM * $heightM), 1);

                if ($bmi < 18.5) {
                    $bmiLabel = 'Kurus';
                    $bmiTone = 'text-amber-600';
                } elseif ($bmi < 25) {
                    $bmiLabel = 'Normal';
                    $bmiTone = 'text-emerald-600';
                } elseif ($bmi < 30) {
                    $bmiLabel = 'Berlebih';
                    $bmiTone = 'text-amber-600';
                } else {
                    $bmiLabel = 'Obesitas';
                    $bmiTone = 'text-rose-600';
                }
            }

            $latestScreening = $user->screeningSessions()
                ->latest()
                ->first();

            $stats = [
                'screening_count' => $user->screeningSessions()->count(),
                'monitoring_count' => $user->healthMonitorings()->count(),
                'bmi' => $bmi,
                'bmi_label' => $bmiLabel,
                'bmi_tone' => $bmiTone,
                'latest_screening' => $latestScreening,
            ];
        }

        return view('profile.index', compact('stats'));
    }
}
