<?php

namespace App\Services;

class MonitoringScoreService
{
    /**
     * @return array<string, int>
     */
    public function severityScoreMap(): array
    {
        $map = [];
        foreach (config('monitoring.severity_options', []) as $option) {
            $map[$option['value']] = (int) $option['score'];
        }

        return $map;
    }

    /**
     * @return array<string, int>
     */
    public function selfManagementScoreMap(): array
    {
        $map = [];
        foreach (config('monitoring.self_management_options', []) as $option) {
            $map[$option['value']] = (int) $option['score'];
        }

        return $map;
    }

    public function complaintMaxScore(string $disease): int
    {
        $symptoms = config("monitoring_complaints.{$disease}", []);

        return count($symptoms) * 3;
    }

    /**
     * @param  array<string, string>  $answers
     */
    public function complaintTotal(string $disease, array $answers): int
    {
        $symptoms = config("monitoring_complaints.{$disease}", []);
        $scoreMap = $this->severityScoreMap();
        $total = 0;

        foreach (array_keys($symptoms) as $key) {
            $value = (string) ($answers[$key] ?? '');
            if ($value === '') {
                $value = 'tidak_ada';
            }
            $total += $scoreMap[$value] ?? 0;
        }

        return $total;
    }

    public function complaintScoreLabel(int $total, string $disease): string
    {
        $max = max(1, $this->complaintMaxScore($disease));
        $percent = ($total / $max) * 100;

        foreach (config('monitoring.complaint_thresholds', []) as $threshold) {
            if ($percent <= $threshold['max_percent']) {
                return $threshold['label'];
            }
        }

        return 'kurang';
    }

    /**
     * @param  array<string, string>  $answers
     */
    public function selfManagementPercent(string $disease, string $riskLevel, array $answers): float
    {
        $items = config("monitoring_self_management.{$disease}.{$riskLevel}.items", []);
        if ($items === []) {
            return 0;
        }

        $scoreMap = $this->selfManagementScoreMap();
        $total = 0;

        foreach (array_keys($items) as $index) {
            $value = (string) ($answers[(string) $index] ?? 'tidak');
            $total += $scoreMap[$value] ?? 0;
        }

        $max = count($items) * 2;

        return round(($total / max(1, $max)) * 100, 1);
    }

    public function percentScoreLabel(float $percent): string
    {
        foreach (config('monitoring.percent_thresholds', []) as $threshold) {
            if ($percent >= $threshold['min']) {
                return $threshold['label'];
            }
        }

        return 'kurang';
    }

    public function relapseScore(string $value): int
    {
        foreach (config('monitoring.relapse_options', []) as $option) {
            if ($option['value'] === $value) {
                return (int) $option['score'];
            }
        }

        return 0;
    }

    public function relapseScoreLabel(int $score): string
    {
        return config("monitoring.relapse_labels.{$score}", 'kurang');
    }

    public function displayScoreLabel(?string $label): string
    {
        if ($label === null) {
            return '-';
        }

        return config("monitoring.score_labels.{$label}", ucfirst($label));
    }

    /**
     * @param  list<\App\Models\HealthMonitoring>  $dailyRecords
     * @return array{
     *     percent: ?float,
     *     label: ?string,
     *     days_on_time: int,
     *     days_recorded: int,
     *     prescription_days: ?int,
     *     expected_days: ?int
     * }
     */
    public function medicationComplianceFromDaily(array $dailyRecords, ?\App\Models\User $user = null): array
    {
        $totalChecks = 0;
        $onTimeChecks = 0;

        foreach ($dailyRecords as $record) {
            if (is_array($record->medication_checks) && $record->medication_checks !== []) {
                foreach ($record->medication_checks as $check) {
                    if (! array_key_exists('on_time', $check)) {
                        continue;
                    }
                    $totalChecks++;
                    if ($check['on_time']) {
                        $onTimeChecks++;
                    }
                }

                continue;
            }

            if ($record->medication_on_time === null) {
                continue;
            }

            $totalChecks++;
            if ($record->medication_on_time) {
                $onTimeChecks++;
            }
        }

        if ($totalChecks === 0) {
            return [
                'percent' => null,
                'label' => null,
                'days_on_time' => 0,
                'days_recorded' => 0,
                'prescription_days' => null,
                'expected_days' => null,
            ];
        }

        $daysRecorded = count(array_filter(
            $dailyRecords,
            fn ($record) => (is_array($record->medication_checks) && $record->medication_checks !== [])
                || $record->medication_on_time !== null,
        ));
        $daysOnTime = count(array_filter(
            $dailyRecords,
            fn ($record) => $this->dailyMedicationCompliancePercent($record) === 100.0,
        ));

        $prescriptionDays = $this->resolvePrescriptionDays($dailyRecords, $user);
        $expectedDays = $prescriptionDays > 0 ? $prescriptionDays : null;

        if ($expectedDays !== null) {
            // Kepatuhan = hari semua obat tepat waktu ÷ durasi resep dokter.
            // Hari tanpa catatan di dalam periode resep dianggap belum patuh.
            $percent = round(min(100, ($daysOnTime / $expectedDays) * 100), 1);
        } else {
            $percent = round(($onTimeChecks / $totalChecks) * 100, 1);
        }

        return [
            'percent' => $percent,
            'label' => $this->percentScoreLabel($percent),
            'days_on_time' => $daysOnTime,
            'days_recorded' => $daysRecorded,
            'prescription_days' => $prescriptionDays > 0 ? $prescriptionDays : null,
            'expected_days' => $expectedDays,
        ];
    }

    /**
     * @param  list<\App\Models\HealthMonitoring>  $dailyRecords
     */
    private function resolvePrescriptionDays(array $dailyRecords, ?\App\Models\User $user = null): ?int
    {
        $fromChecks = collect($dailyRecords)
            ->flatMap(fn ($record) => is_array($record->medication_checks) ? $record->medication_checks : [])
            ->pluck('prescription_days')
            ->filter(fn ($days) => $days !== null && $days !== '')
            ->map(fn ($days) => (int) $days);

        $fromColumn = collect($dailyRecords)
            ->pluck('medication_prescription_days')
            ->filter(fn ($days) => $days !== null && $days !== '')
            ->map(fn ($days) => (int) $days);

        $max = $fromChecks->merge($fromColumn)->max();

        if ($user !== null) {
            $fromProfile = $user->activeMedications()
                ->pluck('prescription_days')
                ->filter(fn ($days) => $days !== null && $days > 0)
                ->max();

            if ($fromProfile !== null) {
                $max = max((int) ($max ?? 0), (int) $fromProfile);
            }
        }

        return ($max ?? 0) > 0 ? (int) $max : null;
    }

    public function dailyMedicationCompliancePercent(?\App\Models\HealthMonitoring $record): ?float
    {
        if (! $record instanceof \App\Models\HealthMonitoring) {
            return null;
        }

        if (is_array($record->medication_checks) && $record->medication_checks !== []) {
            $checks = array_values(array_filter(
                $record->medication_checks,
                fn ($check) => array_key_exists('on_time', $check),
            ));

            if ($checks === []) {
                return null;
            }

            $onTime = count(array_filter($checks, fn ($check) => (bool) $check['on_time']));

            return round(($onTime / count($checks)) * 100, 1);
        }

        if ($record->medication_on_time === null) {
            return null;
        }

        return $record->medication_on_time ? 100.0 : 0.0;
    }

    /**
     * @param  list<\App\Models\HealthMonitoring>  $dailyRecords
     */
    public function aggregateComplaintFromDaily(array $dailyRecords): array
    {
        $records = array_filter($dailyRecords, fn ($r) => $r->complaint_total !== null);
        if ($records === []) {
            return ['total' => null, 'label' => null];
        }

        $latest = end($records);

        return [
            'total' => $latest->complaint_total,
            'label' => $latest->complaint_score_label,
        ];
    }

    /**
     * @param  list<\App\Models\HealthMonitoring>  $dailyRecords
     */
    public function aggregateSelfManagementFromDaily(array $dailyRecords): array
    {
        $records = array_filter($dailyRecords, fn ($r) => $r->self_management_percent !== null);
        if ($records === []) {
            return ['percent' => null, 'label' => null];
        }

        $avg = array_sum(array_map(fn ($r) => (float) $r->self_management_percent, $records)) / count($records);

        return [
            'percent' => round($avg, 1),
            'label' => $this->percentScoreLabel($avg),
        ];
    }

    public function vitalsSummary(?\App\Models\HealthMonitoring $record): ?string
    {
        if (! $record instanceof \App\Models\HealthMonitoring) {
            return null;
        }

        $parts = [];
        if ($record->bloodPressureLabel()) {
            $parts[] = 'TD '.$record->bloodPressureLabel();
        }
        if ($record->heart_rate) {
            $parts[] = "Nadi {$record->heart_rate} bpm";
        }
        if ($record->temperature) {
            $parts[] = "Suhu {$record->temperature}°C";
        }
        if ($record->respiratory_rate) {
            $parts[] = "RR {$record->respiratory_rate}/menit";
        }
        if ($record->blood_sugar) {
            $parts[] = "GDS {$record->blood_sugar} mg/dL";
        }
        if ($record->oxygen_saturation) {
            $parts[] = "SpO₂ {$record->oxygen_saturation}%";
        }
        if ($record->weight) {
            $parts[] = "BB {$record->weight} kg";
        }

        return $parts === [] ? null : implode(' · ', $parts);
    }

    /**
     * @return array{raw: string, narrative: string}|null
     */
    public function vitalsNarrativeSummary(?\App\Models\HealthMonitoring $record): ?array
    {
        if (! $record instanceof \App\Models\HealthMonitoring) {
            return null;
        }

        $raw = $this->vitalsSummary($record);
        if ($raw === null) {
            return null;
        }

        $insights = [];

        if ($record->systolic && $record->diastolic) {
            if ($record->systolic >= 140 || $record->diastolic >= 90) {
                $insights[] = 'Tekanan darah terlihat tinggi — pertimbangkan konsultasi jika berulang.';
            } elseif ($record->systolic < 90 || $record->diastolic < 60) {
                $insights[] = 'Tekanan darah terlihat rendah — perhatikan pusing atau lemas.';
            } else {
                $insights[] = 'Tekanan darah dalam rentang wajar.';
            }
        }

        if ($record->heart_rate) {
            if ($record->heart_rate > 100) {
                $insights[] = 'Nadi sedikit cepat.';
            } elseif ($record->heart_rate < 60) {
                $insights[] = 'Nadi relatif lambat.';
            }
        }

        if ($record->temperature && (float) $record->temperature >= 37.5) {
            $insights[] = 'Suhu tubuh sedikit meningkat.';
        }

        if ($record->oxygen_saturation && $record->oxygen_saturation < 95) {
            $insights[] = 'SpO₂ di bawah normal — waspadai sesak napas.';
        }

        if ($record->blood_sugar) {
            if ((float) $record->blood_sugar >= 200) {
                $insights[] = 'Gula darah tinggi — jaga pola makan dan obat.';
            } elseif ((float) $record->blood_sugar < 70) {
                $insights[] = 'Gula darah rendah — waspadai gejala hipoglikemia.';
            }
        }

        return [
            'raw' => $raw,
            'narrative' => $insights === []
                ? 'Parameter vital tercatat dalam batas umum.'
                : implode(' ', $insights),
        ];
    }
}
