<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HealthMonitoring;
use App\Services\AdminStatsService;
use App\Services\MonitoringDerivedFieldsSync;
use App\Support\AppTimezone;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminMonitoringController extends Controller
{
    public function __construct(
        private AdminStatsService $stats,
        private MonitoringDerivedFieldsSync $derivedFieldsSync,
    ) {}

    public function index(Request $request): View
    {
        $this->derivedFieldsSync->syncStaleRecords();

        $filters = [
            'q' => trim((string) $request->query('q', '')),
            'type' => (string) $request->query('type', ''),
            'disease' => (string) $request->query('disease', ''),
        ];

        $periodRange = $this->stats->resolveAdminPeriodRange(
            $request->query('period_from') ? (string) $request->query('period_from') : null,
            $request->query('period_to') ? (string) $request->query('period_to') : null,
            $request->filled('period_month') ? (string) $request->query('period_month') : null,
        );

        $showMonthlyDetail = $periodRange !== null;
        $periodFrom = $periodRange['from_input'] ?? now()->startOfMonth()->format('Y-m-d');
        $periodTo = $periodRange['to_input'] ?? now()->format('Y-m-d');
        $periodMonth = $periodRange ? $periodRange['from']->format('Y-m') : now()->format('Y-m');

        $diseases = array_keys(config('monitoring_complaints', []));
        $charts = $this->stats->monitoringCharts(
            $filters['type'] !== '' ? $filters['type'] : null,
            $filters['disease'] !== '' ? $filters['disease'] : null,
            $filters['q'],
            $periodRange['from'] ?? null,
            $periodRange['to'] ?? null,
        );
        $monthlyOverview = $showMonthlyDetail && $periodRange
            ? $this->stats->adminPeriodOverview(
                $periodRange['from'],
                $periodRange['to'],
                $filters['disease'] !== '' ? $filters['disease'] : null,
                $filters['q'],
            )
            : null;

        $entries = HealthMonitoring::query()
            ->with('user')
            ->when($periodRange, function ($query) use ($periodRange) {
                return $this->stats->applyMonitoringPeriodFilterForQuery(
                    $query,
                    $periodRange['from'],
                    $periodRange['to'],
                );
            })
            ->when($filters['q'] !== '', function ($query) use ($filters) {
                $query->whereHas('user', function ($q) use ($filters) {
                    $q->where('name', 'like', "%{$filters['q']}%")
                        ->orWhere('email', 'like', "%{$filters['q']}%");
                });
            })
            ->when($filters['type'] === 'daily', fn ($query) => $query->daily())
            ->when($filters['type'] === 'monthly', fn ($query) => $query->monthly())
            ->when($filters['type'] !== '' && ! in_array($filters['type'], ['daily', 'monthly'], true), fn ($query) => $query->where('monitor_type', $filters['type']))
            ->when($filters['disease'] !== '', fn ($query) => $query->where('disease', $filters['disease']))
            ->orderByRaw(AppTimezone::monitoringActivityDateSql().' DESC')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.monitoring.index', [
            'entries' => $entries,
            'filters' => $filters,
            'periodMonth' => $periodMonth,
            'periodFrom' => $periodFrom,
            'periodTo' => $periodTo,
            'showMonthlyDetail' => $showMonthlyDetail,
            'monthlyOverview' => $monthlyOverview,
            'diseases' => $diseases,
            'charts' => $charts,
            'stats' => $this->stats,
        ]);
    }

    public function show(HealthMonitoring $monitoring): View
    {
        $monitoring->load('user');

        return view('admin.monitoring.show', [
            'entry' => $monitoring,
            'complaintRows' => $monitoring->complaintBreakdown(),
            'selfManagementRows' => $monitoring->selfManagementBreakdown(),
        ]);
    }
}
