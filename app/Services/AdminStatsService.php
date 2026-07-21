<?php

namespace App\Services;

use App\Models\ConsultationOrder;
use App\Models\HealthMonitoring;
use App\Models\ScreeningIdentity;
use App\Models\ScreeningSession;
use App\Models\User;
use App\Models\MedicineOrder;
use App\Models\HomecareBooking;
use App\Support\AppTimezone;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class AdminStatsService
{
    private function hasStructuredMonitoring(): bool
    {
        return Schema::hasColumn('health_monitorings', 'monitor_type');
    }

    private function hasConsultationOrders(): bool
    {
        return Schema::hasTable('consultation_orders');
    }

    /**
     * @return array<string, mixed>
     */
    public function overview(): array
    {
        $screeningsByDisease = $this->screeningsByDisease();
        $monitoringByDisease = $this->hasStructuredMonitoring()
            ? $this->monitoringByDisease()
            : collect();
        $monitoringAveragesByDisease = $this->hasStructuredMonitoring()
            ? $this->monitoringAveragesByDisease()
            : collect();

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
            'screeningsByDisease' => $screeningsByDisease,
            'screeningsByRisk' => $this->screeningsByRisk(),
            'screeningsOverTime' => $this->screeningsOverTime(),
            'screeningsGuestVsRegistered' => $this->screeningsGuestVsRegistered(),
            'monitoringOverTime' => $this->monitoringOverTime(),
            'monitoringByDisease' => $monitoringByDisease,
            'monitoringByType' => $this->monitoringByType(),
            'monitoringTypeDonutItems' => $this->hasStructuredMonitoring()
                ? $this->monitoringTypeDonutItems(HealthMonitoring::query())
                : [],
            'monitoringAveragesByDisease' => $monitoringAveragesByDisease,
            'riskChartItems' => $this->screeningRiskDonutItems(ScreeningSession::query()),
            'diseaseBarItems' => $this->screeningDiseaseBarItems(ScreeningSession::query()),
            'monitoringDiseaseItems' => $this->hasStructuredMonitoring()
                ? $this->monitoringDiseaseBarItems(HealthMonitoring::query())
                : [],
            'monitoringAverageBars' => $this->monitoringAverageBarItems($monitoringAveragesByDisease),
            'recentScreenings' => ScreeningSession::query()
                ->with(['user', 'identity'])
                ->latest()
                ->limit(8)
                ->get(),
            'recentUsers' => User::query()
                ->where('is_admin', false)
                ->withCount(['screeningSessions', 'healthMonitorings'])
                ->latest()
                ->limit(5)
                ->get(),
            'recentMonitoring' => HealthMonitoring::query()
                ->with('user')
                ->orderByRaw(AppTimezone::monitoringActivityDateSql().' DESC')
                ->orderByDesc('id')
                ->limit(5)
                ->get(),
            'patientActivitySummaries' => $this->activitySummaries(),
            'consultationPendingCount' => $this->hasConsultationOrders()
                ? ConsultationOrder::query()
                    ->where('status', 'pending')
                    ->where('payment_method', 'dana')
                    ->count()
                : 0,
            'consultationPaidCount' => $this->hasConsultationOrders()
                ? ConsultationOrder::query()
                    ->where('status', 'paid')
                    ->where('payment_method', 'dana')
                    ->count()
                : 0,
            'recentConsultationOrders' => $this->hasConsultationOrders()
                ? ConsultationOrder::query()
                    ->with('user')
                    ->where('payment_method', 'dana')
                    ->latest('created_at')
                    ->limit(6)
                    ->get()
                : collect(),
            'pendingConsultationOrders' => $this->hasConsultationOrders()
                ? ConsultationOrder::query()
                    ->with('user')
                    ->where('payment_method', 'dana')
                    ->where('status', 'pending')
                    ->latest('created_at')
                    ->limit(5)
                    ->get()
                : collect(),
            'medicinePendingCount' => Schema::hasTable('medicine_orders')
                ? MedicineOrder::query()
                    ->where('status', 'pending')
                    ->whereNotNull('payment_proof')
                    ->count()
                : 0,
            'homecarePendingCount' => Schema::hasTable('homecare_bookings')
                ? HomecareBooking::query()
                    ->where('status', 'pending')
                    ->whereNotNull('payment_proof')
                    ->count()
                : 0,
        ];
    }

    /**
     * Rekap aktivitas semua subjek: pasien terdaftar, admin, dan tamu (identitas skrining).
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function activitySummaries(int $limit = 10): Collection
    {
        return $this->userActivitySummaries()
            ->concat($this->guestActivitySummaries())
            ->sortByDesc(fn (array $summary) => $this->activityScore($summary))
            ->take($limit)
            ->values();
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function userActivitySummaries(): Collection
    {
        $withCounts = [
            'screeningSessions',
            'screeningSessions as initial_screenings_count' => fn ($q) => $q->where('disease', 'skrining_awal'),
            'screeningSessions as disease_screenings_count' => fn ($q) => $q->where('disease', '!=', 'skrining_awal')->whereNotNull('disease'),
            'healthMonitorings',
            'selfManagementLogs',
            'selfManagementLogs as completed_self_management_count' => fn ($q) => $q->where('completed', true),
        ];

        if (Schema::hasColumn('health_monitorings', 'monitor_type')) {
            $withCounts['healthMonitorings as daily_monitorings_count'] = fn ($q) => $q->where(function ($inner) {
                $inner->where('monitor_type', 'daily')->orWhereNull('monitor_type');
            });
            $withCounts['healthMonitorings as monthly_monitorings_count'] = fn ($q) => $q->where('monitor_type', 'monthly');
        }

        if (Schema::hasTable('user_medications')) {
            $withCounts['medications'] = fn ($q) => $q;
            $withCounts['medications as active_medications_count'] = fn ($q) => $q->where('is_active', true);
        }

        $users = User::query()
            ->withCount($withCounts)
            ->where(function ($q) {
                $q->whereHas('screeningSessions')
                    ->orWhereHas('healthMonitorings')
                    ->orWhereHas('selfManagementLogs');
                if (Schema::hasTable('user_medications')) {
                    $q->orWhereHas('medications');
                }
            })
            ->get();

        if ($users->isEmpty()) {
            return collect();
        }

        $userIds = $users->pluck('id');
        $screeningByUser = $this->screeningDiseaseCounts('user_id', $userIds);
        $monitoringByUser = $this->monitoringDiseaseCounts('user_id', $userIds);

        return $users->map(function (User $user) use ($screeningByUser, $monitoringByUser) {
            return $this->buildActivitySummary(
                name: $user->name,
                subjectLabel: $user->is_admin ? 'Admin' : 'Pasien',
                subjectType: $user->is_admin ? 'admin' : 'patient',
                detailUrl: $user->is_admin
                    ? route('admin.screenings.index', ['q' => $user->name])
                    : route('admin.users.show', $user),
                photoUrl: $user->profilePhotoUrl(),
                counts: $user,
                screeningDiseases: $this->formatDiseaseCounts($screeningByUser->get($user->id, collect())),
                monitoringDiseases: $this->formatDiseaseCounts($monitoringByUser->get($user->id, collect())),
            );
        });
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function guestActivitySummaries(): Collection
    {
        $identities = ScreeningIdentity::query()
            ->withCount([
                'screeningSessions as screening_sessions_count' => fn ($q) => $q->whereNull('user_id'),
                'screeningSessions as initial_screenings_count' => fn ($q) => $q->whereNull('user_id')->where('disease', 'skrining_awal'),
                'screeningSessions as disease_screenings_count' => fn ($q) => $q->whereNull('user_id')->where('disease', '!=', 'skrining_awal')->whereNotNull('disease'),
            ])
            ->whereHas('screeningSessions', fn ($q) => $q->whereNull('user_id'))
            ->get();

        if ($identities->isEmpty()) {
            return collect();
        }

        $identityIds = $identities->pluck('id');
        $screeningByIdentity = ScreeningSession::query()
            ->whereIn('screening_identity_id', $identityIds)
            ->whereNull('user_id')
            ->whereNotNull('disease')
            ->where('disease', '!=', 'skrining_awal')
            ->selectRaw('screening_identity_id, disease, COUNT(*) as total')
            ->groupBy('screening_identity_id', 'disease')
            ->orderByDesc('total')
            ->get()
            ->groupBy('screening_identity_id');

        return $identities->map(function (ScreeningIdentity $identity) use ($screeningByIdentity) {
            $gender = strtolower((string) $identity->gender);
            $isFemale = in_array($gender, ['perempuan', 'female', 'p'], true);

            return $this->buildActivitySummary(
                name: $identity->name ?? 'Tamu',
                subjectLabel: 'Tamu',
                subjectType: 'guest',
                detailUrl: route('admin.screenings.index', ['q' => $identity->name ?? '']),
                photoUrl: asset($isFemale ? 'images/avatars/female.svg' : 'images/avatars/male.svg'),
                counts: $identity,
                screeningDiseases: $this->formatDiseaseCounts($screeningByIdentity->get($identity->id, collect())),
                monitoringDiseases: '',
            );
        });
    }

    /**
     * @param  User|ScreeningIdentity  $counts
     * @return array<string, mixed>
     */
    private function buildActivitySummary(
        string $name,
        string $subjectLabel,
        string $subjectType,
        string $detailUrl,
        string $photoUrl,
        object $counts,
        string $screeningDiseases,
        string $monitoringDiseases,
    ): array {
        return [
            'name' => $name,
            'subject_label' => $subjectLabel,
            'subject_type' => $subjectType,
            'detail_url' => $detailUrl,
            'photo_url' => $photoUrl,
            'initial_screenings_count' => (int) ($counts->initial_screenings_count ?? 0),
            'disease_screenings_count' => (int) ($counts->disease_screenings_count ?? 0),
            'daily_monitorings_count' => (int) ($counts->daily_monitorings_count ?? 0),
            'monthly_monitorings_count' => (int) ($counts->monthly_monitorings_count ?? 0),
            'medications_count' => (int) ($counts->medications_count ?? 0),
            'active_medications_count' => (int) ($counts->active_medications_count ?? 0),
            'self_management_logs_count' => (int) ($counts->self_management_logs_count ?? 0),
            'completed_self_management_count' => (int) ($counts->completed_self_management_count ?? 0),
            'screening_diseases' => $screeningDiseases,
            'monitoring_diseases' => $monitoringDiseases,
        ];
    }

    /**
     * @param  \Illuminate\Support\Collection<int, int|string>  $ids
     */
    private function screeningDiseaseCounts(string $column, Collection $ids): Collection
    {
        return ScreeningSession::query()
            ->whereIn($column, $ids)
            ->whereNotNull('disease')
            ->where('disease', '!=', 'skrining_awal')
            ->selectRaw("{$column}, disease, COUNT(*) as total")
            ->groupBy($column, 'disease')
            ->orderByDesc('total')
            ->get()
            ->groupBy($column);
    }

    /**
     * @param  \Illuminate\Support\Collection<int, int|string>  $ids
     */
    private function monitoringDiseaseCounts(string $column, Collection $ids): Collection
    {
        return HealthMonitoring::query()
            ->whereIn($column, $ids)
            ->whereNotNull('disease')
            ->selectRaw("{$column}, disease, COUNT(*) as total")
            ->groupBy($column, 'disease')
            ->orderByDesc('total')
            ->get()
            ->groupBy($column);
    }

    /**
     * @param  \Illuminate\Support\Collection<int, object{disease: string, total: int|string}>  $rows
     */
    private function formatDiseaseCounts(Collection $rows): string
    {
        return $rows
            ->map(fn ($row) => $this->diseaseLabel($row->disease).' ('.$row->total.')')
            ->implode(', ');
    }

    /**
     * @param  array<string, mixed>  $summary
     */
    private function activityScore(array $summary): int
    {
        return (int) $summary['initial_screenings_count']
            + (int) $summary['disease_screenings_count']
            + (int) $summary['daily_monitorings_count']
            + (int) $summary['monthly_monitorings_count']
            + (int) $summary['self_management_logs_count']
            + (int) $summary['medications_count'];
    }

    /** @deprecated Use activitySummaries() */
    public function patientActivitySummaries(int $limit = 10): Collection
    {
        return $this->activitySummaries($limit);
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

    /**
     * @return list<array{date: string, label: string, value: int}>
     */
    public function screeningsOverTime(int $days = 14): array
    {
        return $this->countOverTime(ScreeningSession::query(), 'created_at', $days);
    }

    /**
     * @return list<array{date: string, label: string, value: int}>
     */
    public function monitoringOverTime(int $days = 14): array
    {
        $start = now()->subDays($days - 1)->startOfDay();

        $activityDate = AppTimezone::monitoringActivityDateSql();

        $rows = HealthMonitoring::query()
            ->whereRaw("{$activityDate} >= ?", [$start->format('Y-m-d')])
            ->selectRaw("{$activityDate} as day, COUNT(*) as total")
            ->groupBy('day')
            ->pluck('total', 'day');

        $data = [];
        for ($i = 0; $i < $days; $i++) {
            $date = $start->copy()->addDays($i);
            $key = $date->format('Y-m-d');
            $data[] = [
                'date' => $date->format('d/m'),
                'label' => $date->translatedFormat('d M'),
                'value' => (int) ($rows[$key] ?? 0),
            ];
        }

        return $data;
    }

    /**
     * Orang unik yang pernah skrining — bukan jumlah sesi.
     *
     * @return array{registered: int, guest: int}
     */
    public function screeningsGuestVsRegistered(): array
    {
        return $this->screeningsGuestVsRegisteredFromQuery(ScreeningSession::query());
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<ScreeningSession>  $query
     * @return array{registered: int, guest: int}
     */
    private function screeningsGuestVsRegisteredFromQuery($query): array
    {
        return [
            'registered' => (clone $query)
                ->whereNotNull('user_id')
                ->distinct()
                ->count('user_id'),
            'guest' => (clone $query)
                ->whereNull('user_id')
                ->whereNotNull('screening_identity_id')
                ->distinct()
                ->count('screening_identity_id'),
        ];
    }

    /**
     * @return Collection<string, int>
     */
    public function monitoringByDisease(): Collection
    {
        return HealthMonitoring::query()
            ->selectRaw('disease, count(*) as total')
            ->whereNotNull('disease')
            ->groupBy('disease')
            ->orderByDesc('total')
            ->pluck('total', 'disease');
    }

    /**
     * @return array{daily: int, monthly: int}
     */
    public function monitoringByType(): array
    {
        if (! Schema::hasColumn('health_monitorings', 'monitor_type')) {
            return [
                'daily' => HealthMonitoring::count(),
                'monthly' => 0,
            ];
        }

        return [
            'daily' => HealthMonitoring::query()->daily()->count(),
            'monthly' => HealthMonitoring::query()->monthly()->count(),
        ];
    }

    /**
     * @return Collection<int, object{disease: string, avg_complaint: float|null, avg_self_management: float|null, avg_compliance: float|null}>
     */
    public function monitoringAveragesByDisease(): Collection
    {
        return $this->monitoringAveragesForQuery(
            HealthMonitoring::query()->daily(),
        );
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<HealthMonitoring>  $dailyQuery
     * @return Collection<int, object{disease: string, avg_complaint: float|null, avg_self_management: float|null, avg_compliance: float|null}>
     */
    private function monitoringAveragesForQuery($dailyQuery): Collection
    {
        $scoreService = app(MonitoringScoreService::class);

        $records = (clone $dailyQuery)
            ->whereNotNull('disease')
            ->get()
            ->groupBy('disease');

        $averages = collect();

        foreach ($records as $disease => $dailyRecords) {
            $complaintRecords = $dailyRecords->whereNotNull('complaint_total');
            $selfRecords = $dailyRecords->whereNotNull('self_management_percent');

            $compliancePercents = [];
            foreach ($dailyRecords->groupBy('user_id') as $userId => $userRecords) {
                $user = $userId ? User::find($userId) : null;
                $medication = $scoreService->medicationComplianceFromDaily($userRecords->all(), $user);
                if ($medication['percent'] !== null) {
                    $compliancePercents[] = $medication['percent'];
                }
            }

            $avgCompliance = $compliancePercents === []
                ? null
                : round(array_sum($compliancePercents) / count($compliancePercents), 1);

            $averages->put($disease, (object) [
                'disease' => $disease,
                'avg_complaint' => $complaintRecords->isEmpty()
                    ? null
                    : $complaintRecords->avg('complaint_total'),
                'avg_self_management' => $selfRecords->isEmpty()
                    ? null
                    : $selfRecords->avg('self_management_percent'),
                'avg_compliance' => $avgCompliance,
            ]);
        }

        return $averages->sortKeys();
    }

    /**
     * @return array{
     *     total: int,
     *     dailyCount: int,
     *     monthlyCount: int,
     *     overTime: list<array{date: string, label: string, value: int}>,
     *     byDisease: Collection<string, int>,
     *     byType: array{daily: int, monthly: int},
     *     averagesByDisease: Collection<int, object{disease: string, avg_complaint: float|null, avg_self_management: float|null, avg_compliance: float|null}>,
     *     averageBarItems: array<string, list<array<string, mixed>>>,
     *     typeDonutItems: list<array<string, mixed>>,
     *     diseaseBarItems: list<array<string, mixed>>,
     *     periodLabel: string
     * }
     */
    public function monitoringCharts(
        ?string $type = null,
        ?string $disease = null,
        string $search = '',
        ?\Carbon\Carbon $periodFrom = null,
        ?\Carbon\Carbon $periodTo = null,
    ): array {
        $query = $this->filteredMonitoringQuery($type, $disease, $search);

        if ($periodFrom && $periodTo) {
            $query = $this->applyMonitoringPeriodFilter($query, $periodFrom, $periodTo);
            $overTime = $this->monitoringOverTimeForRange(clone $query, $periodFrom, $periodTo);
            $periodLabel = $periodFrom->isSameDay($periodTo)
                ? $periodFrom->translatedFormat('d M Y')
                : $periodFrom->translatedFormat('d M Y').' – '.$periodTo->translatedFormat('d M Y');
        } else {
            $overTime = $this->monitoringOverTimeForQuery(clone $query, 14);
            $periodLabel = '14 hari';
        }

        $dailyQuery = (clone $query)->daily();

        return [
            'total' => (clone $query)->count(),
            'dailyCount' => (clone $query)->daily()->count(),
            'monthlyCount' => (clone $query)->monthly()->count(),
            'overTime' => $overTime,
            'byDisease' => (clone $query)
                ->selectRaw('disease, count(*) as total')
                ->whereNotNull('disease')
                ->groupBy('disease')
                ->orderByDesc('total')
                ->pluck('total', 'disease'),
            'byType' => [
                'daily' => (clone $query)->daily()->count(),
                'monthly' => (clone $query)->monthly()->count(),
            ],
            'averagesByDisease' => $this->monitoringAveragesForQuery(clone $dailyQuery),
            'averageBarItems' => $this->monitoringAverageBarItems(
                $this->monitoringAveragesForQuery(clone $dailyQuery),
            ),
            'typeDonutItems' => $this->monitoringTypeDonutItems(clone $query),
            'diseaseBarItems' => $this->monitoringDiseaseBarItems(clone $query),
            'periodLabel' => $periodLabel,
        ];
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<HealthMonitoring>  $query
     * @return \Illuminate\Database\Eloquent\Builder<HealthMonitoring>
     */
    public function applyMonitoringPeriodFilterForQuery($query, \Carbon\Carbon $from, \Carbon\Carbon $to)
    {
        return $this->applyMonitoringPeriodFilter($query, $from, $to);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<HealthMonitoring>  $query
     * @return \Illuminate\Database\Eloquent\Builder<HealthMonitoring>
     */
    private function applyMonitoringPeriodFilter($query, \Carbon\Carbon $from, \Carbon\Carbon $to)
    {
        $fromDate = $from->format('Y-m-d');
        $toDate = $to->format('Y-m-d');
        $fromMonth = $from->format('Y-m');
        $toMonth = $to->format('Y-m');
        $activityDate = AppTimezone::monitoringActivityDateSql();

        if (! $this->hasStructuredMonitoring()) {
            return $query
                ->whereRaw("{$activityDate} >= ?", [$fromDate])
                ->whereRaw("{$activityDate} <= ?", [$toDate]);
        }

        return $query->where(function ($period) use ($fromDate, $toDate, $fromMonth, $toMonth, $activityDate) {
            $period->where(function ($daily) use ($fromDate, $toDate, $activityDate) {
                $daily->daily()
                    ->whereRaw("{$activityDate} >= ?", [$fromDate])
                    ->whereRaw("{$activityDate} <= ?", [$toDate]);
            })->orWhere(function ($monthly) use ($fromMonth, $toMonth) {
                $monthly->monthly()
                    ->where('period_month', '>=', $fromMonth)
                    ->where('period_month', '<=', $toMonth);
            });
        });
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<HealthMonitoring>  $query
     * @return list<array{date: string, label: string, value: int}>
     */
    private function monitoringOverTimeForRange($query, \Carbon\Carbon $from, \Carbon\Carbon $to): array
    {
        $fromDate = $from->format('Y-m-d');
        $toDate = $to->format('Y-m-d');
        $activityDate = AppTimezone::monitoringActivityDateSql();

        $rows = (clone $query)
            ->whereRaw("{$activityDate} >= ?", [$fromDate])
            ->whereRaw("{$activityDate} <= ?", [$toDate])
            ->selectRaw("{$activityDate} as day, COUNT(*) as total")
            ->groupBy('day')
            ->pluck('total', 'day');

        $data = [];
        $cursor = $from->copy();
        while ($cursor->lte($to)) {
            $key = $cursor->format('Y-m-d');
            $data[] = [
                'date' => $cursor->format('d/m'),
                'label' => $cursor->translatedFormat('d M'),
                'value' => (int) ($rows[$key] ?? 0),
            ];
            $cursor->addDay();
        }

        return $data;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder<HealthMonitoring>
     */
    private function filteredMonitoringQuery(?string $type, ?string $disease, string $search)
    {
        return HealthMonitoring::query()
            ->when($type === 'daily', fn ($q) => $q->daily())
            ->when($type === 'monthly', fn ($q) => $q->monthly())
            ->when($type !== null && $type !== '' && ! in_array($type, ['daily', 'monthly'], true), fn ($q) => $q->where('monitor_type', $type))
            ->when($disease !== null && $disease !== '', fn ($q) => $q->where('disease', $disease))
            ->when($search !== '', function ($query) use ($search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            });
    }

    /**
     * @return array{
     *     total: int,
     *     highRiskCount: int,
     *     emergencyCount: int,
     *     overTime: list<array{date: string, label: string, value: int}>,
     *     byDisease: Collection<string, int>,
     *     byRisk: Collection<string, int>,
     *     guestVsRegistered: array{registered: int, guest: int}
     * }
     */
    public function screeningCharts(?string $disease = null, ?string $risk = null, string $search = ''): array
    {
        $query = $this->filteredScreeningQuery($disease, $risk, $search);

        return [
            'total' => (clone $query)->count(),
            'highRiskCount' => (clone $query)->whereIn('risk_level', ['high', 'emergency'])->count(),
            'emergencyCount' => (clone $query)->where('is_emergency', true)->count(),
            'overTime' => $this->countOverTime(clone $query, 'created_at', 14),
            'byDisease' => (clone $query)
                ->selectRaw('disease, count(*) as total')
                ->whereNotNull('disease')
                ->groupBy('disease')
                ->orderByDesc('total')
                ->pluck('total', 'disease'),
            'byRisk' => (clone $query)
                ->selectRaw('risk_level, count(*) as total')
                ->groupBy('risk_level')
                ->orderByDesc('total')
                ->pluck('total', 'risk_level'),
            'guestVsRegistered' => $this->screeningsGuestVsRegisteredFromQuery(clone $query),
            'riskDonutItems' => $this->screeningRiskDonutItems(clone $query),
            'diseaseBarItems' => $this->screeningDiseaseBarItems(clone $query),
        ];
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<HealthMonitoring>  $query
     * @return list<array{date: string, label: string, value: int}>
     */
    private function monitoringOverTimeForQuery($query, int $days): array
    {
        $start = now()->subDays($days - 1)->startOfDay();

        $activityDate = AppTimezone::monitoringActivityDateSql();

        $rows = (clone $query)
            ->whereRaw("{$activityDate} >= ?", [$start->format('Y-m-d')])
            ->selectRaw("{$activityDate} as day, COUNT(*) as total")
            ->groupBy('day')
            ->pluck('total', 'day');

        $data = [];
        for ($i = 0; $i < $days; $i++) {
            $date = $start->copy()->addDays($i);
            $key = $date->format('Y-m-d');
            $data[] = [
                'date' => $date->format('d/m'),
                'label' => $date->translatedFormat('d M'),
                'value' => (int) ($rows[$key] ?? 0),
            ];
        }

        return $data;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder<ScreeningSession>
     */
    private function filteredScreeningQuery(?string $disease, ?string $risk, string $search)
    {
        return ScreeningSession::query()
            ->when($disease, fn ($q) => $q->where('disease', $disease))
            ->when($risk, fn ($q) => $q->where('risk_level', $risk))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('identity', fn ($i) => $i->where('name', 'like', "%{$search}%"));
                });
            });
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>  $query
     * @return list<array{date: string, label: string, value: int}>
     */
    private function countOverTime($query, string $column, int $days): array
    {
        $start = now()->subDays($days - 1)->startOfDay();

        $daySql = AppTimezone::sqlDateFromUtcTimestamp($column);

        $rows = (clone $query)
            ->where($column, '>=', $start)
            ->selectRaw("{$daySql} as day, COUNT(*) as total")
            ->groupBy('day')
            ->pluck('total', 'day');

        $data = [];
        for ($i = 0; $i < $days; $i++) {
            $date = $start->copy()->addDays($i);
            $key = $date->format('Y-m-d');
            $data[] = [
                'date' => $date->format('d/m'),
                'label' => $date->translatedFormat('d M'),
                'value' => (int) ($rows[$key] ?? 0),
            ];
        }

        return $data;
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<ScreeningSession>  $query
     * @return list<array{label: string, value: int, color: string, breakdown: list<array{label: string, value: int}>}>
     */
    public function screeningRiskDonutItems($query): array
    {
        $total = max(1, (clone $query)->count());

        $byRisk = (clone $query)
            ->selectRaw('risk_level, count(*) as total')
            ->groupBy('risk_level')
            ->pluck('total', 'risk_level');

        $byRiskDisease = (clone $query)
            ->selectRaw('risk_level, disease, count(*) as total')
            ->whereNotNull('disease')
            ->groupBy('risk_level', 'disease')
            ->get()
            ->groupBy('risk_level');

        $colors = [
            'low' => '#059669',
            'medium' => '#d97706',
            'high' => '#ea580c',
            'emergency' => '#e11d48',
        ];

        $items = [];
        foreach (['low', 'medium', 'high', 'emergency'] as $level) {
            $count = (int) ($byRisk[$level] ?? 0);
            if ($count === 0) {
                continue;
            }

            $breakdown = collect($byRiskDisease->get($level, collect()))
                ->map(fn ($row) => [
                    'label' => $this->diseaseLabel($row->disease),
                    'value' => (int) $row->total,
                ])
                ->sortByDesc('value')
                ->values()
                ->all();

            $items[] = [
                'label' => $this->riskLabel($level),
                'value' => $count,
                'percent' => round($count / $total * 100, 1),
                'color' => $colors[$level] ?? '#64748b',
                'breakdown' => $breakdown,
            ];
        }

        return $items;
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<ScreeningSession>  $query
     * @return list<array{label: string, value: int, percent: float, display: string, color: string, breakdown: list<array{label: string, value: int}>}>
     */
    public function screeningDiseaseBarItems($query): array
    {
        $total = max(1, (clone $query)->whereNotNull('disease')->count());

        $byDisease = (clone $query)
            ->selectRaw('disease, count(*) as total')
            ->whereNotNull('disease')
            ->groupBy('disease')
            ->orderByDesc('total')
            ->pluck('total', 'disease');

        $byDiseaseRisk = (clone $query)
            ->selectRaw('disease, risk_level, count(*) as total')
            ->whereNotNull('disease')
            ->groupBy('disease', 'risk_level')
            ->get()
            ->groupBy('disease');

        $riskOrder = ['low', 'medium', 'high', 'emergency'];

        $items = [];
        foreach ($byDisease as $disease => $count) {
            $count = (int) $count;
            $percent = round($count / $total * 100, 1);

            $breakdown = [];
            $riskRows = collect($byDiseaseRisk->get($disease, collect()));
            foreach ($riskOrder as $level) {
                $row = $riskRows->firstWhere('risk_level', $level);
                $riskCount = $row ? (int) $row->total : 0;
                if ($riskCount > 0) {
                    $breakdown[] = [
                        'label' => $this->riskLabel($level),
                        'value' => $riskCount,
                    ];
                }
            }

            $items[] = [
                'label' => $this->diseaseLabel($disease),
                'value' => $count,
                'percent' => $percent,
                'display' => $count.' · '.$percent.'%',
                'color' => '#0066ff',
                'breakdown' => $breakdown,
            ];
        }

        return $items;
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<HealthMonitoring>  $query
     * @return list<array{label: string, value: int, percent: float, color: string, breakdown: list<array{label: string, value: int}>}>
     */
    public function monitoringTypeDonutItems($query): array
    {
        $total = max(1, (clone $query)->count());

        $byType = (clone $query)
            ->selectRaw('monitor_type, count(*) as total')
            ->groupBy('monitor_type')
            ->pluck('total', 'monitor_type');

        $byTypeDisease = (clone $query)
            ->selectRaw('monitor_type, disease, count(*) as total')
            ->whereNotNull('disease')
            ->groupBy('monitor_type', 'disease')
            ->get()
            ->groupBy('monitor_type');

        $types = [
            'daily' => ['label' => 'Harian', 'color' => '#059669'],
            'monthly' => ['label' => 'Bulanan', 'color' => '#7c3aed'],
        ];

        $items = [];
        foreach ($types as $key => $meta) {
            $count = (int) ($byType[$key] ?? 0);
            if ($count === 0) {
                continue;
            }

            $breakdown = collect($byTypeDisease->get($key, collect()))
                ->map(fn ($row) => [
                    'label' => $this->diseaseLabel($row->disease),
                    'value' => (int) $row->total,
                ])
                ->sortByDesc('value')
                ->values()
                ->all();

            $items[] = [
                'label' => $meta['label'],
                'value' => $count,
                'percent' => round($count / $total * 100, 1),
                'color' => $meta['color'],
                'breakdown' => $breakdown,
            ];
        }

        return $items;
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<HealthMonitoring>  $query
     * @return list<array{label: string, value: int, percent: float, display: string, color: string, breakdown: list<array{label: string, value: int}>}>
     */
    public function monitoringDiseaseBarItems($query): array
    {
        $total = max(1, (clone $query)->whereNotNull('disease')->count());

        $byDisease = (clone $query)
            ->selectRaw('disease, count(*) as total')
            ->whereNotNull('disease')
            ->groupBy('disease')
            ->orderByDesc('total')
            ->pluck('total', 'disease');

        $byDiseaseType = (clone $query)
            ->selectRaw('disease, monitor_type, count(*) as total')
            ->whereNotNull('disease')
            ->groupBy('disease', 'monitor_type')
            ->get()
            ->groupBy('disease');

        $typeLabels = [
            'daily' => 'Harian',
            'monthly' => 'Bulanan',
        ];

        $items = [];
        foreach ($byDisease as $disease => $count) {
            $count = (int) $count;
            $percent = round($count / $total * 100, 1);

            $breakdown = [];
            foreach ($typeLabels as $type => $label) {
                $typeCount = (int) optional(
                    collect($byDiseaseType->get($disease, collect()))->firstWhere('monitor_type', $type)
                )->total ?? 0;

                if ($typeCount > 0) {
                    $breakdown[] = [
                        'label' => $label,
                        'value' => $typeCount,
                    ];
                }
            }

            $items[] = [
                'label' => $this->diseaseLabel($disease),
                'value' => $count,
                'percent' => $percent,
                'display' => $count.' · '.$percent.'%',
                'color' => '#7c3aed',
                'breakdown' => $breakdown,
            ];
        }

        return $items;
    }

    /**
     * @return array{
     *     from: \Carbon\Carbon,
     *     to: \Carbon\Carbon,
     *     label: string,
     *     from_input: string,
     *     to_input: string,
     *     days: int
     * }|null
     */
    public function resolveAdminPeriodRange(?string $from, ?string $to, ?string $legacyMonth = null): ?array
    {
        if (($from === null || $from === '') && ($to === null || $to === '') && $legacyMonth !== null && preg_match('/^\d{4}-\d{2}$/', $legacyMonth)) {
            $start = \Carbon\Carbon::createFromFormat('Y-m', $legacyMonth)->startOfMonth();
            $from = $start->format('Y-m-d');
            $to = $start->copy()->endOfMonth()->format('Y-m-d');
        }

        if ($from === null || $to === null || $from === '' || $to === '') {
            return null;
        }

        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $from) || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $to)) {
            return null;
        }

        try {
            $start = \Carbon\Carbon::createFromFormat('Y-m-d', $from)->startOfDay();
            $end = \Carbon\Carbon::createFromFormat('Y-m-d', $to)->startOfDay();
        } catch (\Throwable) {
            return null;
        }

        if ($start->gt($end)) {
            [$start, $end] = [$end, $start];
        }

        $days = (int) $start->diffInDays($end) + 1;
        if ($days > 366) {
            $end = $start->copy()->addDays(365);
            $days = 366;
        }

        $label = $start->isSameDay($end)
            ? $start->translatedFormat('d M Y')
            : $start->translatedFormat('d M Y').' – '.$end->translatedFormat('d M Y');

        return [
            'from' => $start,
            'to' => $end,
            'label' => $label,
            'from_input' => $start->format('Y-m-d'),
            'to_input' => $end->format('Y-m-d'),
            'days' => $days,
        ];
    }

    /**
     * @return array{
     *     period_from: string,
     *     period_to: string,
     *     period_label: string,
     *     period_days: int,
     *     period_month: string,
     *     month_label: string,
     *     daily_count: int,
     *     monthly_count: int,
     *     chart_data: list<array{date: string, value: int}>,
     *     disease_summaries: list<array<string, mixed>>
     * }
     */
    public function adminPeriodOverview(
        \Carbon\Carbon $periodFrom,
        \Carbon\Carbon $periodTo,
        ?string $disease = null,
        string $search = '',
    ): array {
        $baseQuery = $this->filteredMonitoringQuery(null, $disease, $search);
        $fromDate = $periodFrom->format('Y-m-d');
        $toDate = $periodTo->format('Y-m-d');
        $fromMonth = $periodFrom->format('Y-m');
        $toMonth = $periodTo->format('Y-m');
        $scoreService = app(MonitoringScoreService::class);

        $activityDate = AppTimezone::monitoringActivityDateSql();

        $dailyInPeriod = (clone $baseQuery)
            ->daily()
            ->whereRaw("{$activityDate} >= ?", [$fromDate])
            ->whereRaw("{$activityDate} <= ?", [$toDate]);

        $monthlyRecords = (clone $baseQuery)
            ->monthly()
            ->where('period_month', '>=', $fromMonth)
            ->where('period_month', '<=', $toMonth);

        $diseaseSlugs = $disease
            ? [$disease]
            : array_keys(config('monitoring_complaints', []));

        $summaries = [];
        foreach ($diseaseSlugs as $slug) {
            $dailyRecords = (clone $dailyInPeriod)
                ->where('disease', $slug)
                ->orderBy('recorded_at')
                ->get()
                ->all();

            $monthlyCount = (clone $monthlyRecords)->where('disease', $slug)->count();

            if ($dailyRecords === [] && $monthlyCount === 0) {
                continue;
            }

            $complaint = $scoreService->aggregateComplaintFromDaily($dailyRecords);
            $selfManagement = $scoreService->aggregateSelfManagementFromDaily($dailyRecords);

            $compliancePercents = [];
            $daysOnTimeTotal = 0;
            $expectedDays = null;
            $prescriptionDays = null;

            foreach (collect($dailyRecords)->groupBy('user_id') as $userId => $userPeriodRecords) {
                if (! $userId) {
                    continue;
                }

                $user = User::find($userId);
                if (! $user) {
                    continue;
                }

                $courseRecords = HealthMonitoring::query()
                    ->daily()
                    ->where('user_id', $userId)
                    ->where('disease', $slug)
                    ->whereRaw(AppTimezone::monitoringActivityDateSql().' >= ?', [$fromDate])
                    ->whereRaw(AppTimezone::monitoringActivityDateSql().' <= ?', [$toDate])
                    ->where(function ($query) {
                        $query->whereNotNull('medication_on_time');

                        if (Schema::hasColumn('health_monitorings', 'medication_checks')) {
                            $query->orWhereNotNull('medication_checks');
                        }
                    })
                    ->orderBy('recorded_at')
                    ->get()
                    ->all();

                $userMedication = $scoreService->medicationComplianceFromDaily($courseRecords, $user);
                if ($userMedication['percent'] === null) {
                    continue;
                }

                $compliancePercents[] = $userMedication['percent'];
                $daysOnTimeTotal += $userMedication['days_on_time'];
                $expectedDays ??= $userMedication['expected_days'];
                $prescriptionDays ??= $userMedication['prescription_days'];
            }

            $avgCompliance = $compliancePercents === []
                ? null
                : round(array_sum($compliancePercents) / count($compliancePercents), 1);

            $medication = [
                'percent' => $avgCompliance,
                'label' => $avgCompliance !== null ? $scoreService->percentScoreLabel($avgCompliance) : null,
                'days_on_time' => $daysOnTimeTotal,
                'days_recorded' => count($dailyRecords),
                'expected_days' => $expectedDays,
                'prescription_days' => $prescriptionDays,
            ];

            $monthlyForDisease = (clone $monthlyRecords)->where('disease', $slug)->get();
            $relapseLabel = $monthlyForDisease
                ->pluck('relapse_score_label')
                ->filter()
                ->countBy()
                ->sortDesc()
                ->keys()
                ->first();

            $summaries[] = [
                'disease' => $slug,
                'label' => $this->diseaseLabel($slug),
                'icon' => config("diseases.{$slug}.icon", '📋'),
                'daily_count' => count($dailyRecords),
                'monthly_count' => $monthlyCount,
                'complaint_total' => $complaint['total'],
                'complaint_label' => $complaint['label'],
                'self_management_percent' => $selfManagement['percent'],
                'self_management_label' => $selfManagement['label'],
                'medication_compliance_percent' => $medication['percent'],
                'medication_compliance_label' => $medication['label'],
                'medication_days_on_time' => $medication['days_on_time'],
                'medication_days_recorded' => $medication['days_recorded'],
                'medication_expected_days' => $medication['expected_days'],
                'prescription_days' => $medication['prescription_days'],
                'relapse_score_label' => $relapseLabel,
            ];
        }

        usort($summaries, fn ($a, $b) => ($b['daily_count'] + $b['monthly_count']) <=> ($a['daily_count'] + $a['monthly_count']));

        $periodLabel = $periodFrom->isSameDay($periodTo)
            ? $periodFrom->translatedFormat('d M Y')
            : $periodFrom->translatedFormat('d M Y').' – '.$periodTo->translatedFormat('d M Y');

        return [
            'period_from' => $fromDate,
            'period_to' => $toDate,
            'period_label' => $periodLabel,
            'period_days' => (int) $periodFrom->diffInDays($periodTo) + 1,
            'period_month' => $periodFrom->format('Y-m'),
            'month_label' => $periodLabel,
            'daily_count' => (clone $dailyInPeriod)->count(),
            'monthly_count' => (clone $monthlyRecords)->count(),
            'chart_data' => $this->adminDailyCountChartForRange(clone $baseQuery, $periodFrom, $periodTo),
            'disease_summaries' => $summaries,
        ];
    }

    /**
     * @deprecated Use adminPeriodOverview()
     *
     * @return array{
     *     period_month: string,
     *     month_label: string,
     *     daily_count: int,
     *     monthly_count: int,
     *     chart_data: list<array{date: string, value: int}>,
     *     disease_summaries: list<array<string, mixed>>
     * }
     */
    public function adminMonthlyOverview(string $periodMonth, ?string $disease = null, string $search = ''): array
    {
        $start = \Carbon\Carbon::createFromFormat('Y-m', $periodMonth)->startOfMonth();

        return $this->adminPeriodOverview(
            $start,
            $start->copy()->endOfMonth(),
            $disease,
            $search,
        );
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<HealthMonitoring>  $query
     * @return list<array{date: string, value: int}>
     */
    private function adminDailyCountChartForRange($query, \Carbon\Carbon $from, \Carbon\Carbon $to): array
    {
        $fromDate = $from->format('Y-m-d');
        $toDate = $to->format('Y-m-d');

        $activityDate = AppTimezone::monitoringActivityDateSql();

        $rows = (clone $query)
            ->daily()
            ->whereRaw("{$activityDate} >= ?", [$fromDate])
            ->whereRaw("{$activityDate} <= ?", [$toDate])
            ->selectRaw("{$activityDate} as day, COUNT(*) as total")
            ->groupBy('day')
            ->pluck('total', 'day');

        $data = [];
        $cursor = $from->copy();
        while ($cursor->lte($to)) {
            $key = $cursor->format('Y-m-d');
            $data[] = [
                'date' => $cursor->format('d/m'),
                'value' => (int) ($rows[$key] ?? 0),
            ];
            $cursor->addDay();
        }

        return $data;
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<HealthMonitoring>  $query
     * @return list<array{date: string, value: int}>
     */
    private function adminDailyCountChartForMonth($query, string $periodMonth): array
    {
        $start = \Carbon\Carbon::createFromFormat('Y-m', $periodMonth)->startOfMonth();

        return $this->adminDailyCountChartForRange($query, $start, $start->copy()->endOfMonth());
    }

    /**
     * @return array{
     *     complaint: list<array{label: string, value: float, display: string, category: string, color: string}>,
     *     selfManagement: list<array{label: string, value: float, display: string, category: string, color: string}>,
     *     compliance: list<array{label: string, value: float, display: string, category: string, color: string}>
     * }
     */
    public function monitoringAverageBarItems(Collection $averages): array
    {
        $scoreService = app(MonitoringScoreService::class);

        $complaint = [];
        $selfManagement = [];
        $compliance = [];

        foreach ($averages as $row) {
            $disease = $row->disease;
            $label = $this->diseaseLabel($disease);

            if ($row->avg_complaint !== null) {
                $avgComplaint = round((float) $row->avg_complaint, 1);
                $maxComplaint = max(1, $scoreService->complaintMaxScore($disease));
                $complaintPercent = round(($avgComplaint / $maxComplaint) * 100, 1);
                $complaintKey = $scoreService->complaintScoreLabel((int) round($avgComplaint), $disease);
                $complaintCategory = $scoreService->displayScoreLabel($complaintKey);

                $complaint[] = [
                    'label' => $label,
                    'value' => $complaintPercent,
                    'display' => "{$avgComplaint} ({$complaintPercent}%)",
                    'category' => $complaintCategory,
                    'color' => $this->scoreCategoryColor($complaintKey),
                ];
            }

            if ($row->avg_self_management !== null) {
                $avgSelfManagement = round((float) $row->avg_self_management, 1);
                $selfKey = $scoreService->percentScoreLabel($avgSelfManagement);
                $selfCategory = $scoreService->displayScoreLabel($selfKey);

                $selfManagement[] = [
                    'label' => $label,
                    'value' => $avgSelfManagement,
                    'display' => "{$avgSelfManagement}%",
                    'category' => $selfCategory,
                    'color' => $this->scoreCategoryColor($selfKey),
                ];
            }

            if ($row->avg_compliance !== null) {
                $avgCompliance = round((float) $row->avg_compliance, 1);
                $complianceKey = $scoreService->percentScoreLabel($avgCompliance);
                $complianceCategory = $scoreService->displayScoreLabel($complianceKey);

                $compliance[] = [
                    'label' => $label,
                    'value' => $avgCompliance,
                    'display' => "{$avgCompliance}%",
                    'category' => $complianceCategory,
                    'color' => $this->scoreCategoryColor($complianceKey),
                ];
            }
        }

        return [
            'complaint' => $complaint,
            'selfManagement' => $selfManagement,
            'compliance' => $compliance,
        ];
    }

    private function scoreCategoryColor(string $key): string
    {
        return match ($key) {
            'baik' => '#059669',
            'cukup' => '#d97706',
            'kurang' => '#e11d48',
            default => '#64748b',
        };
    }
}
