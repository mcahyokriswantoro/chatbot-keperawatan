<?php

namespace App\Services;

use App\Models\HealthMonitoring;
use Illuminate\Support\Facades\Schema;

class MonitoringDerivedFieldsSync
{
    public function __construct(
        private MonitoringScoreService $scores,
    ) {}

    /**
     * Sinkronkan field turunan (kepatuhan obat, summary) dari data mentah user.
     */
    public function syncStaleRecords(): int
    {
        if (! Schema::hasColumn('health_monitorings', 'medication_compliance_percent')) {
            return 0;
        }

        $updated = 0;

        HealthMonitoring::query()
            ->where(function ($query) {
                $query->whereNotNull('medication_on_time');

                if (Schema::hasColumn('health_monitorings', 'medication_checks')) {
                    $query->orWhereNotNull('medication_checks');
                }
            })
            ->where(function ($query) {
                $query->whereNull('medication_compliance_percent')
                    ->orWhereNull('medication_compliance_label');
            })
            ->orderBy('id')
            ->chunkById(100, function ($records) use (&$updated) {
                foreach ($records as $record) {
                    if ($this->syncRecord($record)) {
                        $updated++;
                    }
                }
            });

        return $updated;
    }

    public function syncRecord(HealthMonitoring $record): bool
    {
        $percent = $this->scores->dailyMedicationCompliancePercent($record);
        if ($percent === null) {
            return false;
        }

        $label = $this->scores->percentScoreLabel($percent);
        $summary = $record->summary_data ?? [];
        $summary['kepatuhan_obat'] = [
            'percent' => $percent,
            'label' => $label,
            'tepat_waktu' => $record->medication_on_time,
            'checks' => $record->medication_checks,
        ];

        $record->forceFill([
            'medication_compliance_percent' => $percent,
            'medication_compliance_label' => $label,
            'summary_data' => $summary,
        ])->save();

        return true;
    }
}
