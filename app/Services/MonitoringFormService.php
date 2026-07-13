<?php

namespace App\Services;

use App\Models\HealthMonitoring;
use App\Models\ScreeningSession;
use App\Models\User;
use App\Models\UserMedication;
use Illuminate\Support\Collection;

class MonitoringFormService
{
    public function __construct(
        private MonitoringScoreService $scores,
    ) {}

    /**
     * @return list<array{slug: string, label: string, icon: string, risk: string}>
     */
    public function userDiseases(User $user): array
    {
        $complaintDiseases = array_keys(config('monitoring_complaints', []));

        return $user->screeningSessions()
            ->whereIn('disease', $complaintDiseases)
            ->where('disease', '!=', 'skrining_awal')
            ->latest()
            ->get()
            ->unique('disease')
            ->map(function (ScreeningSession $session) {
                $config = config("diseases.{$session->disease}", []);

                return [
                    'slug' => $session->disease,
                    'label' => $config['label'] ?? $session->disease,
                    'icon' => $config['icon'] ?? '📋',
                    'risk' => $session->selfManagementRiskKey() ?? 'Rendah',
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return Collection<int, UserMedication>
     */
    public function userMedications(User $user): Collection
    {
        $this->migrateLegacyMedicationProfile($user);

        return $user->activeMedications()->get();
    }

    /**
     * @return array{name: ?string, dose: ?string, schedule: ?string, prescription_days: ?int}
     */
    public function medicationDefaults(User $user): array
    {
        $medication = $this->userMedications($user)->first();

        if ($medication) {
            return [
                'name' => $medication->name,
                'dose' => $medication->dose,
                'schedule' => $medication->schedule,
                'prescription_days' => $medication->prescription_days,
            ];
        }

        $latest = $user->healthMonitorings()
            ->where('monitor_type', 'daily')
            ->whereNotNull('medication_name')
            ->latest('recorded_at')
            ->first();

        return [
            'name' => $latest?->medication_name,
            'dose' => $latest?->medication_dose,
            'schedule' => $latest?->medication_schedule,
            'prescription_days' => $latest?->medication_prescription_days,
        ];
    }

    private function migrateLegacyMedicationProfile(User $user): void
    {
        if ($user->medications()->exists()) {
            return;
        }

        $latest = $user->healthMonitorings()
            ->where('monitor_type', 'daily')
            ->whereNotNull('medication_name')
            ->latest('recorded_at')
            ->first();

        if (! $latest) {
            return;
        }

        $user->medications()->create([
            'name' => $latest->medication_name,
            'dose' => $latest->medication_dose,
            'schedule' => $latest->medication_schedule,
            'prescription_days' => $latest->medication_prescription_days,
            'sort_order' => 0,
        ]);
    }

    /**
     * @return Collection<int, HealthMonitoring>
     */
    public function courseDailyRecords(User $user, string $disease): Collection
    {
        return $user->healthMonitorings()
            ->where('monitor_type', 'daily')
            ->where('disease', $disease)
            ->where(function ($query) {
                $query->whereNotNull('medication_on_time')
                    ->orWhereNotNull('medication_checks');
            })
            ->orderBy('recorded_at')
            ->get();
    }

    /**
     * @return Collection<int, HealthMonitoring>
     */
    public function dailyRecordsForMonth(User $user, string $disease, string $month): Collection
    {
        return $user->healthMonitorings()
            ->where('monitor_type', 'daily')
            ->where('disease', $disease)
            ->whereYear('recorded_at', (int) substr($month, 0, 4))
            ->whereMonth('recorded_at', (int) substr($month, 5, 2))
            ->orderBy('recorded_at')
            ->get();
    }

    /**
     * @return list<array{date: string, label: string, complaint: ?int, self_management: ?float, compliance: ?float}>
     */
    public function chartData(User $user, string $disease, int $days = 14): array
    {
        $records = $user->healthMonitorings()
            ->where('monitor_type', 'daily')
            ->where('disease', $disease)
            ->where('recorded_at', '>=', now()->subDays($days - 1)->startOfDay())
            ->orderBy('recorded_at')
            ->get();

        return $records->map(fn (HealthMonitoring $r) => [
            'date' => $r->recorded_at->format('d/m'),
            'label' => $r->recorded_at->translatedFormat('d M'),
            'complaint' => $r->complaint_total,
            'self_management' => $r->self_management_percent !== null ? (float) $r->self_management_percent : null,
            'compliance' => $this->scores->dailyMedicationCompliancePercent($r),
        ])->all();
    }

    /**
     * @return list<array{date: string, label: string, complaint: ?int, self_management: ?float, compliance: ?float}>
     */
    public function chartDataForMonth(User $user, string $disease, string $month): array
    {
        return $this->dailyRecordsForMonth($user, $disease, $month)
            ->map(fn (HealthMonitoring $r) => [
                'date' => $r->recorded_at->format('d/m'),
                'label' => $r->recorded_at->translatedFormat('d M'),
                'complaint' => $r->complaint_total,
                'self_management' => $r->self_management_percent !== null ? (float) $r->self_management_percent : null,
                'compliance' => $this->scores->dailyMedicationCompliancePercent($r),
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    public function monthlyPreview(User $user, string $disease, string $month): array
    {
        $daily = $this->dailyRecordsForMonth($user, $disease, $month)->all();
        $courseDaily = $this->courseDailyRecords($user, $disease)->all();
        $complaint = $this->scores->aggregateComplaintFromDaily($daily);
        $selfManagement = $this->scores->aggregateSelfManagementFromDaily($daily);
        $medication = $this->scores->medicationComplianceFromDaily($courseDaily, $user);
        $latestVitals = collect($daily)->last();

        $monthDaysRecorded = count(array_filter(
            $daily,
            fn ($record) => (is_array($record->medication_checks) && $record->medication_checks !== [])
                || $record->medication_on_time !== null,
        ));

        $monthlyRecord = $user->healthMonitorings()
            ->where('monitor_type', 'monthly')
            ->where('disease', $disease)
            ->where('period_month', $month)
            ->first();

        return [
            'complaint_total' => $complaint['total'],
            'complaint_label' => $complaint['label'],
            'self_management_percent' => $selfManagement['percent'],
            'self_management_label' => $selfManagement['label'],
            'medication_compliance_percent' => $medication['percent'],
            'medication_compliance_label' => $medication['label'],
            'medication_days_on_time' => $medication['days_on_time'],
            'medication_days_recorded' => $medication['days_recorded'],
            'medication_days_recorded_in_month' => $monthDaysRecorded,
            'medication_expected_days' => $medication['expected_days'],
            'prescription_days' => $medication['prescription_days'],
            'vitals_summary' => $this->scores->vitalsSummary($latestVitals ?: null),
            'daily_count' => count($daily),
            'chart_data' => $this->chartDataForMonth($user, $disease, $month),
            'relapse_frequency' => $monthlyRecord?->relapse_frequency,
            'relapse_score' => $monthlyRecord?->relapse_score,
            'relapse_score_label' => $monthlyRecord?->relapse_score_label,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function dailyResultsSummary(User $user, string $disease, string $month, int $days = 14): array
    {
        $today = $user->healthMonitorings()
            ->where('monitor_type', 'daily')
            ->where('disease', $disease)
            ->whereDate('recorded_at', today())
            ->first();

        $latestInPeriod = $user->healthMonitorings()
            ->where('monitor_type', 'daily')
            ->where('disease', $disease)
            ->where('recorded_at', '>=', now()->subDays($days - 1)->startOfDay())
            ->latest('recorded_at')
            ->first();

        $display = $today ?? $latestInPeriod;

        $monthlyRecord = $user->healthMonitorings()
            ->where('monitor_type', 'monthly')
            ->where('disease', $disease)
            ->where('period_month', $month)
            ->first();

        $monthMedication = $this->scores->medicationComplianceFromDaily(
            $this->courseDailyRecords($user, $disease)->all(),
            $user,
        );

        $vitals = $this->scores->vitalsNarrativeSummary($display);

        $medPercent = null;
        $medLabel = null;
        if ($display) {
            $medPercent = $this->scores->dailyMedicationCompliancePercent($display);
            if ($medPercent !== null) {
                $medLabel = $this->scores->percentScoreLabel($medPercent);
            }
        }
        if ($medPercent === null && $monthMedication['percent'] !== null) {
            $medPercent = $monthMedication['percent'];
            $medLabel = $monthMedication['label'];
        }

        return [
            'has_today' => $today !== null,
            'has_data' => $display !== null,
            'is_latest_fallback' => $today === null && $latestInPeriod !== null,
            'recorded_at_label' => $display?->recorded_at?->translatedFormat('d M Y'),
            'complaint_total' => $display?->complaint_total,
            'complaint_label' => $display?->complaint_score_label,
            'relapse_score' => $monthlyRecord?->relapse_score,
            'relapse_label' => $monthlyRecord?->relapse_score_label,
            'medication_compliance_percent' => $medPercent,
            'medication_compliance_label' => $medLabel,
            'self_management_percent' => $display?->self_management_percent !== null
                ? (float) $display->self_management_percent
                : null,
            'self_management_label' => $display?->self_management_score_label,
            'vitals_raw' => $vitals['raw'] ?? null,
            'vitals_narrative' => $vitals['narrative'] ?? null,
        ];
    }
}
