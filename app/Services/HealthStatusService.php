<?php

namespace App\Services;

use App\Models\HealthMonitoring;
use App\Models\ScreeningSession;
use App\Models\User;

class HealthStatusService
{
    /**
     * @return list<array{label: string, value: string, tone: string, bg: string, icon: string}>
     */
    public function forUser(?User $user): array
    {
        if (! $user) {
            return $this->guestStatus();
        }

        return [
            $this->generalHealth($user),
            $this->bloodPressure($user),
            $this->activity($user),
        ];
    }

    /**
     * @return list<array{label: string, value: string, tone: string, bg: string, icon: string}>
     */
    protected function guestStatus(): array
    {
        $placeholder = [
            'value' => 'Masuk dulu',
            'tone' => 'text-slate-400',
            'bg' => 'bg-slate-50',
        ];

        return [
            array_merge($placeholder, ['label' => 'Kesehatan Umum', 'icon' => 'heart']),
            array_merge($placeholder, ['label' => 'Tekanan Darah', 'icon' => 'bp']),
            array_merge($placeholder, ['label' => 'Aktivitas', 'icon' => 'activity']),
        ];
    }

    /**
     * @return array{label: string, value: string, tone: string, bg: string, icon: string}
     */
    protected function generalHealth(User $user): array
    {
        $latest = $user->screeningSessions()->latest()->first();

        if (! $latest instanceof ScreeningSession) {
            return [
                'label' => 'Kesehatan Umum',
                'value' => 'Belum skrining',
                'tone' => 'text-slate-400',
                'bg' => 'bg-rose-50',
                'icon' => 'heart',
            ];
        }

        if ($latest->showsEmergencyUi()) {
            return [
                'label' => 'Kesehatan Umum',
                'value' => 'Darurat',
                'tone' => 'text-rose-600',
                'bg' => 'bg-rose-50',
                'icon' => 'heart',
            ];
        }

        return match ($latest->displayRiskLevel()) {
            'low' => [
                'label' => 'Kesehatan Umum',
                'value' => 'Baik',
                'tone' => 'text-emerald-600',
                'bg' => 'bg-rose-50',
                'icon' => 'heart',
            ],
            'medium' => [
                'label' => 'Kesehatan Umum',
                'value' => 'Perlu perhatian',
                'tone' => 'text-amber-600',
                'bg' => 'bg-rose-50',
                'icon' => 'heart',
            ],
            'high' => [
                'label' => 'Kesehatan Umum',
                'value' => 'Kurang baik',
                'tone' => 'text-rose-600',
                'bg' => 'bg-rose-50',
                'icon' => 'heart',
            ],
            default => [
                'label' => 'Kesehatan Umum',
                'value' => $latest->scoreLabel(),
                'tone' => 'text-amber-600',
                'bg' => 'bg-rose-50',
                'icon' => 'heart',
            ],
        };
    }

    /**
     * @return array{label: string, value: string, tone: string, bg: string, icon: string}
     */
    protected function bloodPressure(User $user): array
    {
        $latest = $user->healthMonitorings()
            ->whereNotNull('systolic')
            ->whereNotNull('diastolic')
            ->latest('recorded_at')
            ->first();

        if (! $latest instanceof HealthMonitoring) {
            return [
                'label' => 'Tekanan Darah',
                'value' => 'Belum tercatat',
                'tone' => 'text-slate-400',
                'bg' => 'bg-violet-50',
                'icon' => 'bp',
            ];
        }

        $systolic = (int) $latest->systolic;
        $diastolic = (int) $latest->diastolic;
        $reading = "{$systolic}/{$diastolic}";

        if ($systolic < 90 || $diastolic < 60) {
            return [
                'label' => 'Tekanan Darah',
                'value' => $reading,
                'tone' => 'text-amber-600',
                'bg' => 'bg-violet-50',
                'icon' => 'bp',
            ];
        }

        if ($systolic < 120 && $diastolic < 80) {
            return [
                'label' => 'Tekanan Darah',
                'value' => $reading,
                'tone' => 'text-emerald-600',
                'bg' => 'bg-violet-50',
                'icon' => 'bp',
            ];
        }

        if ($systolic < 130 && $diastolic < 80) {
            return [
                'label' => 'Tekanan Darah',
                'value' => $reading,
                'tone' => 'text-amber-600',
                'bg' => 'bg-violet-50',
                'icon' => 'bp',
            ];
        }

        if ($systolic < 140 && $diastolic < 90) {
            return [
                'label' => 'Tekanan Darah',
                'value' => $reading,
                'tone' => 'text-amber-600',
                'bg' => 'bg-violet-50',
                'icon' => 'bp',
            ];
        }

        return [
            'label' => 'Tekanan Darah',
            'value' => $reading,
            'tone' => 'text-rose-600',
            'bg' => 'bg-violet-50',
            'icon' => 'bp',
        ];
    }

    /**
     * @return array{label: string, value: string, tone: string, bg: string, icon: string}
     */
    protected function activity(User $user): array
    {
        $since = now()->subDays(7)->startOfDay();

        $monitoringCount = $user->healthMonitorings()
            ->where('recorded_at', '>=', $since)
            ->count();

        $activeLogs = $user->healthMonitorings()
            ->where('recorded_at', '>=', $since)
            ->whereNotNull('activities')
            ->where('activities', '!=', '')
            ->count();

        $completedTasks = $user->selfManagementLogs()
            ->where('completed', true)
            ->where('scheduled_for', '>=', $since)
            ->count();

        $score = $monitoringCount + min($activeLogs, 2) + min($completedTasks, 3);

        if ($score === 0) {
            return [
                'label' => 'Aktivitas',
                'value' => 'Belum tercatat',
                'tone' => 'text-slate-400',
                'bg' => 'bg-emerald-50',
                'icon' => 'activity',
            ];
        }

        if ($score <= 2) {
            return [
                'label' => 'Aktivitas',
                'value' => 'Kurang',
                'tone' => 'text-amber-600',
                'bg' => 'bg-emerald-50',
                'icon' => 'activity',
            ];
        }

        if ($score <= 4) {
            return [
                'label' => 'Aktivitas',
                'value' => 'Cukup',
                'tone' => 'text-emerald-600',
                'bg' => 'bg-emerald-50',
                'icon' => 'activity',
            ];
        }

        return [
            'label' => 'Aktivitas',
            'value' => 'Aktif',
            'tone' => 'text-emerald-600',
            'bg' => 'bg-emerald-50',
            'icon' => 'activity',
        ];
    }
}
