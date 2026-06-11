<?php

namespace App\Services;

use App\Models\HealthMonitoring;
use App\Models\ScreeningIdentity;
use App\Models\ScreeningSession;
use App\Models\User;
use Illuminate\Support\Collection;

class AdminStatsService
{
    /**
     * @return array<string, mixed>
     */
    public function overview(): array
    {
        return [
            'userCount' => User::query()->where('is_admin', false)->count(),
            'adminCount' => User::query()->where('is_admin', true)->count(),
            'newUsersWeek' => User::query()
                ->where('is_admin', false)
                ->where('created_at', '>=', now()->subDays(7))
                ->count(),
            'screeningCount' => ScreeningSession::count(),
            'identityCount' => ScreeningIdentity::count(),
            'monitoringCount' => HealthMonitoring::count(),
            'emergencyCount' => ScreeningSession::query()->where('is_emergency', true)->count(),
            'guestScreenings' => ScreeningSession::query()->whereNull('user_id')->count(),
            'highRiskCount' => ScreeningSession::query()
                ->whereIn('risk_level', ['high', 'emergency'])
                ->count(),
            'screeningsByDisease' => $this->screeningsByDisease(),
            'screeningsByRisk' => $this->screeningsByRisk(),
            'recentScreenings' => ScreeningSession::query()
                ->with(['user', 'identity'])
                ->latest()
                ->limit(8)
                ->get(),
            'recentUsers' => User::query()
                ->where('is_admin', false)
                ->latest()
                ->limit(5)
                ->get(),
            'recentMonitoring' => HealthMonitoring::query()
                ->with('user')
                ->latest('recorded_at')
                ->limit(5)
                ->get(),
        ];
    }

    /**
     * @return Collection<string, int>
     */
    public function screeningsByDisease(): Collection
    {
        return ScreeningSession::query()
            ->selectRaw('disease, count(*) as total')
            ->whereNotNull('disease')
            ->groupBy('disease')
            ->orderByDesc('total')
            ->pluck('total', 'disease');
    }

    /**
     * @return Collection<string, int>
     */
    public function screeningsByRisk(): Collection
    {
        return ScreeningSession::query()
            ->selectRaw('risk_level, count(*) as total')
            ->groupBy('risk_level')
            ->orderByDesc('total')
            ->pluck('total', 'risk_level');
    }

    public function diseaseLabel(?string $disease): string
    {
        if (! $disease) {
            return '—';
        }

        return config("diseases.{$disease}.label", ucfirst(str_replace('_', ' ', $disease)));
    }

    public function riskLabel(string $level): string
    {
        return match ($level) {
            'low' => 'Rendah',
            'medium' => 'Sedang',
            'high' => 'Tinggi',
            'emergency' => 'Darurat',
            default => ucfirst($level),
        };
    }
}
