<?php

namespace App\Http\Controllers;

use App\Services\MonitoringFormService;
use App\Services\MonitoringScoreService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HealthMonitoringController extends Controller
{
    public function __construct(
        private MonitoringFormService $formService,
        private MonitoringScoreService $scoreService,
    ) {}

    public function index(): View
    {
        $user = auth()->user();
        $diseases = $this->formService->userDiseases($user);
        $medicationDefaults = $this->formService->medicationDefaults($user);
        $userMedications = $this->formService->userMedications($user);
        $currentMonth = now()->format('Y-m');

        $monthlyPreviews = [];
        foreach ($diseases as $disease) {
            $monthlyPreviews[$disease['slug']] = $this->formService->monthlyPreview(
                $user,
                $disease['slug'],
                $currentMonth,
            );
        }

        $chartData = [];
        $dailySummaries = [];
        foreach ($diseases as $disease) {
            $chartData[$disease['slug']] = $this->formService->chartData($user, $disease['slug']);
            $dailySummaries[$disease['slug']] = $this->formService->dailyResultsSummary(
                $user,
                $disease['slug'],
                $currentMonth,
            );
        }

        $records = $user->healthMonitorings()
            ->latest('recorded_at')
            ->paginate(10);

        return view('monitoring.index', [
            'diseases' => $diseases,
            'medicationDefaults' => $medicationDefaults,
            'userMedications' => $userMedications,
            'severityOptions' => config('monitoring.severity_options', []),
            'selfManagementOptions' => config('monitoring.self_management_options', []),
            'relapseOptions' => config('monitoring.relapse_options', []),
            'currentMonth' => $currentMonth,
            'monthlyPreviews' => $monthlyPreviews,
            'chartData' => $chartData,
            'dailySummaries' => $dailySummaries,
            'records' => $records,
        ]);
    }

    public function preview(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'disease' => ['required', 'string', 'in:'.implode(',', array_keys(config('monitoring_complaints', [])))],
            'month' => ['required', 'date_format:Y-m'],
        ]);

        $user = auth()->user();
        $allowed = collect($this->formService->userDiseases($user))->pluck('slug');

        if (! $allowed->contains($validated['disease'])) {
            abort(403);
        }

        return response()->json(
            $this->formService->monthlyPreview($user, $validated['disease'], $validated['month']),
        );
    }

    public function store(Request $request): RedirectResponse
    {
        $monitorType = $request->input('monitor_type', 'daily');

        if ($monitorType === 'monthly') {
            return $this->storeMonthly($request);
        }

        return $this->storeDaily($request);
    }

    private function storeDaily(Request $request): RedirectResponse
    {
        $base = $request->validate([
            'disease' => ['required', 'string', 'in:'.implode(',', array_keys(config('monitoring_complaints', [])))],
            'recorded_at' => ['required', 'date'],
            'save_section' => ['required', 'in:complaints,medication,vitals,self_management'],
        ]);

        $section = $base['save_section'];
        $disease = $base['disease'];
        $recordedAt = $base['recorded_at'];

        $user = auth()->user();
        $existing = $user->healthMonitorings()
            ->where('monitor_type', 'daily')
            ->where('disease', $disease)
            ->whereDate('recorded_at', $recordedAt)
            ->first();

        $sectionData = $this->validateDailySection($request, $section);
        $riskLevel = collect($this->formService->userDiseases($user))
            ->firstWhere('slug', $disease)['risk'] ?? 'Rendah';
        $defaults = $this->formService->medicationDefaults($user);

        $complaintAnswers = $section === 'complaints'
            ? ($sectionData['complaint'] ?? [])
            : ($existing?->complaint_answers ?? []);

        $selfAnswers = $section === 'self_management'
            ? ($sectionData['self_management'] ?? [])
            : ($existing?->self_management_answers ?? []);

        $medicationPayload = $section === 'medication'
            ? $this->processMedicationSection($user, $sectionData['medications'])
            : [
                'checks' => $existing?->medication_checks,
                'on_time' => $existing?->medication_on_time,
                'name' => $existing?->medication_name ?? $defaults['name'],
                'dose' => $existing?->medication_dose ?? $defaults['dose'],
                'schedule' => $existing?->medication_schedule ?? $defaults['schedule'],
                'prescription_days' => $existing?->medication_prescription_days ?? $defaults['prescription_days'],
            ];

        $medicationOnTime = $medicationPayload['on_time'];

        $vitalsFields = ['systolic', 'diastolic', 'heart_rate', 'temperature', 'respiratory_rate', 'blood_sugar', 'oxygen_saturation', 'weight'];
        $vitals = [];
        foreach ($vitalsFields as $field) {
            $vitals[$field] = $section === 'vitals'
                ? ($sectionData[$field] ?? null)
                : ($existing?->{$field});
        }

        $complaintTotal = $this->scoreService->complaintTotal($disease, $complaintAnswers);
        $complaintLabel = ! empty($complaintAnswers)
            ? $this->scoreService->complaintScoreLabel($complaintTotal, $disease)
            : $existing?->complaint_score_label;

        $selfPercent = ! empty($selfAnswers)
            ? $this->scoreService->selfManagementPercent($disease, $riskLevel, $selfAnswers)
            : $existing?->self_management_percent;
        $selfLabel = $selfPercent !== null
            ? $this->scoreService->percentScoreLabel($selfPercent)
            : $existing?->self_management_score_label;

        $medCompliancePercent = null;
        $medComplianceLabel = null;
        $hasMedicationData = ! empty($medicationPayload['checks']) || $medicationOnTime !== null;
        if ($hasMedicationData) {
            $medCompliancePercent = $this->scoreService->dailyMedicationCompliancePercent(
                new \App\Models\HealthMonitoring([
                    'medication_checks' => $medicationPayload['checks'] ?? null,
                    'medication_on_time' => $medicationOnTime,
                ]),
            );
            $medComplianceLabel = $medCompliancePercent !== null
                ? $this->scoreService->percentScoreLabel($medCompliancePercent)
                : null;
        } elseif ($existing?->medication_compliance_percent !== null) {
            $medCompliancePercent = $existing->medication_compliance_percent;
            $medComplianceLabel = $existing->medication_compliance_label;
        }

        $summary = [
            'keluhan' => ! empty($complaintAnswers)
                ? ['skor' => $complaintTotal, 'label' => $complaintLabel]
                : null,
            'self_management' => $selfPercent !== null
                ? ['percent' => $selfPercent, 'label' => $selfLabel]
                : null,
            'kepatuhan_obat' => $medCompliancePercent === null
                ? null
                : [
                    'percent' => $medCompliancePercent,
                    'label' => $medComplianceLabel,
                    'tepat_waktu' => $medicationOnTime,
                    'checks' => $medicationPayload['checks'] ?? null,
                ],
            'tanda_vital' => $this->scoreService->vitalsSummary(new \App\Models\HealthMonitoring($vitals)),
        ];

        $user->healthMonitorings()->updateOrCreate(
            [
                'monitor_type' => 'daily',
                'disease' => $disease,
                'recorded_at' => $recordedAt,
            ],
            [
                'complaint_answers' => $complaintAnswers ?: null,
                'complaint_total' => ! empty($complaintAnswers) ? $complaintTotal : $existing?->complaint_total,
                'complaint_score_label' => $complaintLabel,
                'self_management_answers' => $selfAnswers ?: null,
                'self_management_percent' => $selfPercent,
                'self_management_score_label' => $selfLabel,
                'medication_name' => $medicationPayload['name'],
                'medication_dose' => $medicationPayload['dose'],
                'medication_schedule' => $medicationPayload['schedule'],
                'medication_prescription_days' => $medicationPayload['prescription_days'],
                'medication_on_time' => $medicationOnTime,
                'medication_checks' => $medicationPayload['checks'],
                'medication_compliance_percent' => $medCompliancePercent,
                'medication_compliance_label' => $medComplianceLabel,
                ...$vitals,
                'summary_data' => $summary,
            ],
        );

        $messages = [
            'complaints' => 'Keluhan berhasil disimpan.',
            'medication' => 'Data obat berhasil disimpan.',
            'vitals' => 'Tanda vital berhasil disimpan.',
            'self_management' => 'Self management berhasil disimpan.',
        ];

        return back()->with('status', $messages[$section]);
    }

    /**
     * @param  list<array<string, mixed>>  $medications
     * @return array{
     *     checks: list<array<string, mixed>>,
     *     on_time: bool,
     *     name: ?string,
     *     dose: ?string,
     *     schedule: ?string,
     *     prescription_days: ?int
     * }
     */
    private function processMedicationSection(\App\Models\User $user, array $medications): array
    {
        $checks = [];
        $allOnTime = true;
        $firstMed = null;
        $sortOrder = 0;

        foreach ($medications as $input) {
            $payload = [
                'name' => trim((string) $input['name']),
                'dose' => filled($input['dose'] ?? null) ? trim((string) $input['dose']) : null,
                'schedule' => filled($input['schedule'] ?? null) ? trim((string) $input['schedule']) : null,
                'prescription_days' => filled($input['prescription_days'] ?? null) ? (int) $input['prescription_days'] : null,
                'purpose' => filled($input['purpose'] ?? null) ? trim((string) $input['purpose']) : null,
                'doctor_name' => filled($input['doctor_name'] ?? null) ? trim((string) $input['doctor_name']) : null,
                'is_active' => true,
                'sort_order' => $sortOrder,
            ];

            $medication = null;
            if (! empty($input['id'])) {
                $medication = $user->medications()->whereKey($input['id'])->first();
            }

            if ($medication) {
                $medication->update($payload);
            } else {
                $medication = $user->medications()->create($payload);
            }

            $onTime = ($input['on_time'] ?? '') === 'ya';
            if (! $onTime) {
                $allOnTime = false;
            }

            $checks[] = [
                'medication_id' => $medication->id,
                'name' => $medication->name,
                'dose' => $medication->dose,
                'schedule' => $medication->schedule,
                'prescription_days' => $medication->prescription_days,
                'purpose' => $medication->purpose,
                'doctor_name' => $medication->doctor_name,
                'on_time' => $onTime,
                'notes' => filled($input['notes'] ?? null) ? trim((string) $input['notes']) : null,
            ];

            $firstMed ??= $medication;
            $sortOrder++;
        }

        return [
            'checks' => $checks,
            'on_time' => $allOnTime,
            'name' => $firstMed?->name,
            'dose' => $firstMed?->dose,
            'schedule' => $firstMed?->schedule,
            'prescription_days' => $firstMed?->prescription_days,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function validateDailySection(Request $request, string $section): array
    {
        $vitalsRules = [
            'systolic' => ['nullable', 'integer', 'min:50', 'max:300'],
            'diastolic' => ['nullable', 'integer', 'min:30', 'max:200'],
            'heart_rate' => ['nullable', 'integer', 'min:30', 'max:250'],
            'temperature' => ['nullable', 'numeric', 'min:30', 'max:45'],
            'respiratory_rate' => ['nullable', 'integer', 'min:5', 'max:80'],
            'blood_sugar' => ['nullable', 'numeric', 'min:0', 'max:600'],
            'oxygen_saturation' => ['nullable', 'integer', 'min:50', 'max:100'],
            'weight' => ['nullable', 'numeric', 'min:0', 'max:500'],
        ];

        return match ($section) {
            'complaints' => $request->validate([
                'complaint' => ['required', 'array'],
                'complaint.*' => ['required', 'in:tidak_ada,ringan,sedang,berat'],
            ]),
            'medication' => $request->validate([
                'medications' => ['required', 'array', 'min:1'],
                'medications.*.id' => ['nullable', 'integer'],
                'medications.*.name' => ['required', 'string', 'max:255'],
                'medications.*.dose' => ['nullable', 'string', 'max:255'],
                'medications.*.schedule' => ['nullable', 'string', 'max:255'],
                'medications.*.prescription_days' => ['required_with:medications.*.name', 'nullable', 'integer', 'min:1', 'max:365'],
                'medications.*.purpose' => ['nullable', 'string', 'max:255'],
                'medications.*.doctor_name' => ['nullable', 'string', 'max:255'],
                'medications.*.on_time' => ['required', 'in:ya,tidak'],
                'medications.*.notes' => ['nullable', 'string', 'max:500'],
            ]),
            'vitals' => $request->validate($vitalsRules),
            'self_management' => $request->validate([
                'self_management' => ['required', 'array'],
                'self_management.*' => ['required', 'in:tidak,sepenuhnya'],
            ]),
        };
    }

    private function storeMonthly(Request $request): RedirectResponse
    {
        $base = $request->validate([
            'disease' => ['required', 'string', 'in:'.implode(',', array_keys(config('monitoring_complaints', [])))],
            'period_month' => ['required', 'date_format:Y-m'],
            'save_section' => ['required', 'in:relapse,vitals,notes'],
        ]);

        $section = $base['save_section'];
        $disease = $base['disease'];
        $month = $base['period_month'];
        $user = auth()->user();

        $preview = $this->formService->monthlyPreview($user, $disease, $month);

        if ($preview['daily_count'] === 0) {
            return back()
                ->withInput()
                ->withErrors(['period_month' => 'Belum ada catatan harian untuk bulan ini. Isi monitoring harian terlebih dahulu.']);
        }

        $existing = $user->healthMonitorings()
            ->where('monitor_type', 'monthly')
            ->where('disease', $disease)
            ->where('period_month', $month)
            ->first();

        $sectionData = $this->validateMonthlySection($request, $section);

        $relapseFrequency = $section === 'relapse'
            ? $sectionData['relapse_frequency']
            : ($existing?->relapse_frequency);

        if ($section !== 'relapse' && $relapseFrequency === null) {
            return back()
                ->withInput()
                ->withErrors(['relapse_frequency' => 'Isi frekuensi kekambuhan terlebih dahulu.']);
        }

        $relapseScore = $relapseFrequency !== null
            ? $this->scoreService->relapseScore($relapseFrequency)
            : null;
        $relapseLabel = $relapseScore !== null
            ? $this->scoreService->relapseScoreLabel($relapseScore)
            : null;

        $vitalsFields = ['systolic', 'diastolic', 'heart_rate', 'temperature', 'respiratory_rate', 'blood_sugar', 'oxygen_saturation', 'weight'];
        $vitals = [];
        foreach ($vitalsFields as $field) {
            $vitals[$field] = $section === 'vitals'
                ? ($sectionData[$field] ?? null)
                : ($existing?->{$field});
        }

        $notes = $section === 'notes'
            ? ($sectionData['notes'] ?? null)
            : ($existing?->notes);

        $vitalsRecord = new \App\Models\HealthMonitoring($vitals);
        $summary = [
            'keluhan' => [
                'skor' => $preview['complaint_total'],
                'label' => $preview['complaint_label'],
            ],
            'frekuensi_kekambuhan' => $relapseScore !== null
                ? ['skor' => $relapseScore, 'label' => $relapseLabel]
                : null,
            'kepatuhan_obat' => [
                'percent' => $preview['medication_compliance_percent'],
                'label' => $preview['medication_compliance_label'],
            ],
            'self_management' => [
                'percent' => $preview['self_management_percent'],
                'label' => $preview['self_management_label'],
            ],
            'tanda_vital' => $this->scoreService->vitalsSummary($vitalsRecord) ?? $preview['vitals_summary'],
        ];

        $user->healthMonitorings()->updateOrCreate(
            [
                'monitor_type' => 'monthly',
                'disease' => $disease,
                'period_month' => $month,
            ],
            [
                'recorded_at' => $month.'-01',
                'relapse_frequency' => $relapseFrequency,
                'relapse_score' => $relapseScore,
                'relapse_score_label' => $relapseLabel,
                'complaint_total' => $preview['complaint_total'],
                'complaint_score_label' => $preview['complaint_label'],
                'self_management_percent' => $preview['self_management_percent'],
                'self_management_score_label' => $preview['self_management_label'],
                'medication_compliance_percent' => $preview['medication_compliance_percent'],
                'medication_compliance_label' => $preview['medication_compliance_label'],
                'medication_prescription_days' => $preview['prescription_days'],
                ...$vitals,
                'notes' => $notes,
                'summary_data' => $summary,
            ],
        );

        $messages = [
            'relapse' => 'Frekuensi kekambuhan berhasil disimpan.',
            'vitals' => 'Tanda vital berhasil disimpan.',
            'notes' => 'Catatan bulanan berhasil disimpan.',
        ];

        return back()->with('status', $messages[$section]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validateMonthlySection(Request $request, string $section): array
    {
        $vitalsRules = [
            'systolic' => ['nullable', 'integer', 'min:50', 'max:300'],
            'diastolic' => ['nullable', 'integer', 'min:30', 'max:200'],
            'heart_rate' => ['nullable', 'integer', 'min:30', 'max:250'],
            'temperature' => ['nullable', 'numeric', 'min:30', 'max:45'],
            'respiratory_rate' => ['nullable', 'integer', 'min:5', 'max:80'],
            'blood_sugar' => ['nullable', 'numeric', 'min:0', 'max:600'],
            'oxygen_saturation' => ['nullable', 'integer', 'min:50', 'max:100'],
            'weight' => ['nullable', 'numeric', 'min:0', 'max:500'],
        ];

        return match ($section) {
            'relapse' => $request->validate([
                'relapse_frequency' => ['required', 'in:tidak_pernah,1_kali,2_kali,3_kali'],
            ]),
            'vitals' => $request->validate($vitalsRules),
            'notes' => $request->validate([
                'notes' => ['nullable', 'string', 'max:2000'],
            ]),
        };
    }
}
