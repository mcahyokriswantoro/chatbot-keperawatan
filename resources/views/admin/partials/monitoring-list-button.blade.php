@props(['entry'])

@php
    $isMonthly = $entry->isMonthly();
    $subjectName = $entry->user?->name ?? 'Pengguna';
    $recordedDate = ($entry->recorded_at ?? $entry->created_at)->translatedFormat('d M Y');
    $diseaseLabel = $entry->diseaseLabel();

    $summaryParts = [];
    if ($entry->complaint_score_label) {
        $summaryParts[] = 'Gejala '.$entry->displayComplaintLabel();
    } elseif ($entry->complaint_total !== null) {
        $summaryParts[] = 'Gejala '.$entry->complaint_total;
    }
    if ($entry->self_management_score_label) {
        $summaryParts[] = 'Mandiri '.$entry->displaySelfManagementLabel();
    } elseif ($entry->self_management_percent !== null) {
        $summaryParts[] = 'Mandiri '.rtrim(rtrim(number_format((float) $entry->self_management_percent, 1, '.', ''), '0'), '.').'%';
    }
    if ($entry->medication_compliance_label) {
        $summaryParts[] = 'Kepatuhan '.$entry->displayMedicationComplianceLabel();
    }
    if ($entry->relapse_score_label) {
        $summaryParts[] = 'Kambuh '.$entry->displayRelapseLabel();
    }
    if ($entry->isDaily()) {
        $meds = $entry->medicationBreakdown();
        if (count($meds) === 1) {
            $summaryParts[] = $meds[0]['on_time'] ? 'Obat tepat waktu' : 'Obat belum';
        } elseif (count($meds) > 1) {
            $onTimeCount = collect($meds)->where('on_time', true)->count();
            $summaryParts[] = "Obat {$onTimeCount}/".count($meds);
        }
    }
    if ($isMonthly && $entry->period_month) {
        $summaryParts[] = 'Periode '.$entry->period_month;
    }

    $summary = implode(' · ', array_filter($summaryParts));
@endphp

<a
    href="{{ route('admin.monitoring.show', $entry) }}"
    @class([
        'flex items-center gap-2.5 rounded-xl border px-3 py-2.5 text-left shadow-sm transition active:scale-[0.99]',
        $isMonthly
            ? 'border-sky-200 bg-sky-50/60 hover:bg-sky-50'
            : 'border-emerald-200 bg-emerald-50/60 hover:bg-emerald-50',
    ])
>
    <span @class([
        'inline-flex shrink-0 items-center rounded-lg px-2 py-1 text-[10px] font-bold uppercase tracking-wide text-white',
        $isMonthly ? 'bg-sky-600' : 'bg-emerald-600',
    ])>
        {{ $entry->monitorTypeLabel() }}
    </span>

    <div class="min-w-0 flex-1">
        <p class="truncate text-xs font-semibold text-slate-900">
            {{ $diseaseLabel ?? $subjectName }}
        </p>
        <p class="truncate text-[10px] text-slate-500">
            @if ($diseaseLabel)
                {{ $subjectName }}
                ·
            @endif
            {{ $recordedDate }}
            @if ($summary !== '')
                · {{ $summary }}
            @endif
        </p>
    </div>

    <svg class="h-3.5 w-3.5 shrink-0 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
    </svg>
</a>
