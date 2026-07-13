<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HealthMonitoring;
use App\Services\AdminStatsService;
use App\Services\MonitoringDerivedFieldsSync;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __construct(
        private AdminStatsService $stats,
        private MonitoringDerivedFieldsSync $derivedFieldsSync,
    ) {}

    public function index(Request $request): View
    {
        $periodRange = $this->stats->resolveAdminPeriodRange(
            $request->query('period_from') ? (string) $request->query('period_from') : null,
            $request->query('period_to') ? (string) $request->query('period_to') : null,
            $request->filled('period_month') ? (string) $request->query('period_month') : null,
        );

        $this->derivedFieldsSync->syncStaleRecords();

        $overview = $this->stats->overview();

        if ($periodRange) {
            $monitoringCharts = $this->stats->monitoringCharts(
                null,
                null,
                '',
                $periodRange['from'],
                $periodRange['to'],
            );

            $overview['monitoringOverTime'] = $monitoringCharts['overTime'];
            $overview['monitoringTypeDonutItems'] = $monitoringCharts['typeDonutItems'];
            $overview['monitoringDiseaseItems'] = $monitoringCharts['diseaseBarItems'];
            $overview['monitoringAverageBars'] = $monitoringCharts['averageBarItems'];
            $overview['monitoringPeriodLabel'] = $monitoringCharts['periodLabel'];
        }

        return view('admin.dashboard', $overview + [
            'stats' => $this->stats,
            'periodFrom' => $periodRange['from_input'] ?? now()->startOfMonth()->format('Y-m-d'),
            'periodTo' => $periodRange['to_input'] ?? now()->format('Y-m-d'),
            'periodMonth' => $periodRange ? $periodRange['from']->format('Y-m') : now()->format('Y-m'),
            'showMonthlyDetail' => $periodRange !== null,
            'monthlyOverview' => $periodRange
                ? $this->stats->adminPeriodOverview($periodRange['from'], $periodRange['to'])
                : null,
            'filters' => [
                'q' => '',
                'type' => '',
                'disease' => '',
            ],
        ]);
    }
}
