<?php

namespace App\Models;

use App\Services\MonitoringScoreService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

class HealthMonitoring extends Model
{
    protected $fillable = [
        'user_id',
        'monitor_type',
        'disease',
        'period_month',
        'complaints',
        'complaint_answers',
        'complaint_total',
        'complaint_score_label',
        'medication_name',
        'medication_dose',
        'medication_schedule',
        'medication_prescription_days',
        'medication_on_time',
        'medication_checks',
        'medication_compliance_percent',
        'medication_compliance_label',
        'relapse_frequency',
        'relapse_score',
        'relapse_score_label',
        'activities',
        'self_management_answers',
        'self_management_percent',
        'self_management_score_label',
        'diet_compliant',
        'diet_notes',
        'systolic',
        'diastolic',
        'heart_rate',
        'temperature',
        'respiratory_rate',
        'blood_sugar',
        'oxygen_saturation',
        'weight',
        'notes',
        'summary_data',
        'recorded_at',
    ];

    protected function casts(): array
    {
        return [
            'recorded_at' => 'date',
            'diet_compliant' => 'boolean',
            'medication_on_time' => 'boolean',
            'medication_checks' => 'array',
            'complaint_answers' => 'array',
            'self_management_answers' => 'array',
            'summary_data' => 'array',
            'blood_sugar' => 'decimal:1',
            'weight' => 'decimal:1',
            'temperature' => 'decimal:1',
            'self_management_percent' => 'decimal:1',
            'medication_compliance_percent' => 'decimal:1',
        ];
    }

    public function isDaily(): bool
    {
        return ($this->monitor_type ?? 'daily') === 'daily';
    }

    public function isMonthly(): bool
    {
        return $this->monitor_type === 'monthly';
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<static>  $query
     * @return \Illuminate\Database\Eloquent\Builder<static>
     */
    public function scopeDaily($query)
    {
        $table = $query->getModel()->getTable();
        if (! Schema::hasColumn($table, 'monitor_type')) {
            return $query;
        }

        return $query->where(function ($inner) {
            $inner->where('monitor_type', 'daily')->orWhereNull('monitor_type');
        });
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<static>  $query
     * @return \Illuminate\Database\Eloquent\Builder<static>
     */
    public function scopeMonthly($query)
    {
        $table = $query->getModel()->getTable();
        if (! Schema::hasColumn($table, 'monitor_type')) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('monitor_type', 'monthly');
    }

    public function activityDate(): \Carbon\Carbon
    {
        $raw = $this->recorded_at ?? $this->created_at;

        return \Carbon\Carbon::parse($raw)->timezone('Asia/Jakarta');
    }

    public function diseaseLabel(): ?string
    {
        if ($this->disease === null) {
            return null;
        }

        return config("diseases.{$this->disease}.label", $this->disease);
    }

    public function bloodPressureLabel(): ?string
    {
        if ($this->systolic && $this->diastolic) {
            return "{$this->systolic}/{$this->diastolic} mmHg";
        }

        return null;
    }

    public function dietCompliantLabel(): ?string
    {
        if ($this->diet_compliant === null) {
            return null;
        }

        return $this->diet_compliant ? 'Ya' : 'Tidak';
    }

    public function displayComplaintLabel(): ?string
    {
        return app(MonitoringScoreService::class)->displayScoreLabel($this->complaint_score_label);
    }

    public function displaySelfManagementLabel(): ?string
    {
        return app(MonitoringScoreService::class)->displayScoreLabel($this->self_management_score_label);
    }

    public function displayMedicationComplianceLabel(): ?string
    {
        return app(MonitoringScoreService::class)->displayScoreLabel($this->medication_compliance_label);
    }

    public function displayRelapseLabel(): ?string
    {
        return app(MonitoringScoreService::class)->displayScoreLabel($this->relapse_score_label);
    }

    public function monitorTypeLabel(): string
    {
        return $this->isMonthly() ? 'Bulanan' : 'Harian';
    }

    public function userRiskLevel(): string
    {
        if (! $this->user_id || ! $this->disease) {
            return 'Rendah';
        }

        return $this->user?->screeningSessions()
            ->where('disease', $this->disease)
            ->latest()
            ->first()
            ?->selfManagementRiskKey() ?? 'Rendah';
    }

    public function severityOptionLabel(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        foreach (config('monitoring.severity_options', []) as $option) {
            if ($option['value'] === $value) {
                return $option['label'];
            }
        }

        return $value;
    }

    public function selfManagementOptionLabel(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        foreach (config('monitoring.self_management_options', []) as $option) {
            if ($option['value'] === $value) {
                return $option['label'];
            }
        }

        return $value;
    }

    /**
     * @return list<array{question: string, answer: string, value: ?string}>
     */
    public function complaintBreakdown(): array
    {
        $answers = $this->complaint_answers ?? [];
        if ($answers === [] || $this->disease === null) {
            return [];
        }

        $questions = config("monitoring_complaints.{$this->disease}", []);
        $rows = [];

        foreach ($questions as $key => $question) {
            $value = $answers[$key] ?? null;
            if ($value === null || $value === '') {
                continue;
            }

            $rows[] = [
                'question' => $question,
                'answer' => $this->severityOptionLabel($value) ?? $value,
                'value' => $value,
            ];
        }

        return $rows;
    }

    /**
     * @return list<array{question: string, answer: string, value: ?string}>
     */
    public function selfManagementBreakdown(): array
    {
        $answers = $this->self_management_answers ?? [];
        if ($answers === [] || $this->disease === null) {
            return [];
        }

        $items = config("monitoring_self_management.{$this->disease}.{$this->userRiskLevel()}.items", []);
        $rows = [];

        foreach ($items as $index => $item) {
            $value = $answers[(string) $index] ?? $answers[$index] ?? null;

            $rows[] = [
                'question' => \App\Support\MonitoringCopy::selfManagementPrompt($item),
                'answer' => $this->selfManagementOptionLabel(is_string($value) ? $value : null) ?? '—',
                'value' => is_string($value) ? $value : null,
            ];
        }

        return $rows;
    }

    /**
     * @return list<array{name: string, dose: ?string, schedule: ?string, on_time: bool, notes: ?string}>
     */
    public function medicationBreakdown(): array
    {
        if (is_array($this->medication_checks) && $this->medication_checks !== []) {
            return collect($this->medication_checks)
                ->map(fn ($check) => [
                    'name' => (string) ($check['name'] ?? 'Obat'),
                    'dose' => $check['dose'] ?? null,
                    'schedule' => $check['schedule'] ?? null,
                    'on_time' => (bool) ($check['on_time'] ?? false),
                    'notes' => $check['notes'] ?? null,
                ])
                ->all();
        }

        if ($this->medication_name || $this->medication_on_time !== null) {
            return [[
                'name' => (string) ($this->medication_name ?? 'Obat'),
                'dose' => $this->medication_dose,
                'schedule' => $this->medication_schedule,
                'on_time' => (bool) $this->medication_on_time,
                'notes' => null,
            ]];
        }

        return [];
    }

    public function relapseFrequencyLabel(): ?string
    {
        if ($this->relapse_frequency === null) {
            return null;
        }

        foreach (config('monitoring.relapse_options', []) as $option) {
            if ($option['value'] === $this->relapse_frequency) {
                return $option['label'];
            }
        }

        return $this->relapse_frequency;
    }

    public function vitalsSummary(): ?string
    {
        return app(MonitoringScoreService::class)->vitalsSummary($this);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
