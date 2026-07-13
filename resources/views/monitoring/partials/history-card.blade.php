@props(['record'])

@php
    $isMonthly = $record->isMonthly();
    $accentBorder = $isMonthly ? 'border-l-sky-500' : 'border-l-emerald-500';
    $typeBadge = $isMonthly
        ? 'bg-sky-100 text-sky-800 ring-sky-200'
        : 'bg-emerald-100 text-emerald-800 ring-emerald-200';
@endphp

<article @class(['mb-3 overflow-hidden rounded-2xl border border-slate-100 border-l-4 bg-white shadow-card', $accentBorder])>
    <div class="flex items-start justify-between gap-3 p-4 pb-3">
        <div class="min-w-0">
            <div class="flex flex-wrap items-center gap-2">
                <p class="font-bold text-slate-900">{{ $record->recorded_at->translatedFormat('d M Y') }}</p>
                <span @class(['rounded-full px-2 py-0.5 text-[10px] font-bold uppercase ring-1', $typeBadge])>
                    {{ $isMonthly ? 'Bulanan' : 'Harian' }}
                </span>
            </div>
            @if ($record->diseaseLabel())
                <p class="mt-1 text-xs font-medium text-brand-700">{{ $record->diseaseLabel() }}</p>
            @endif
            @if ($isMonthly && $record->period_month)
                <p class="mt-0.5 text-[10px] text-slate-400">Periode {{ $record->period_month }}</p>
            @endif
        </div>
    </div>

    @if ($record->complaint_total !== null || $record->complaint_score_label || $record->self_management_percent !== null || $record->medication_compliance_percent !== null || $record->relapse_score_label)
        <div class="grid grid-cols-2 gap-2 border-t border-slate-100 bg-slate-50/50 px-4 py-3">
            @if ($record->complaint_total !== null || $record->complaint_score_label)
                <div class="rounded-xl bg-white px-2.5 py-2 ring-1 ring-slate-100">
                    <p class="text-[9px] font-bold uppercase text-slate-400">Gejala</p>
                    <div class="mt-1 flex items-end justify-between gap-1">
                        <span class="text-sm font-bold text-slate-900">{{ $record->complaint_total ?? '—' }}</span>
                        <x-monitoring.score-badge :label="$record->complaint_score_label" />
                    </div>
                </div>
            @endif
            @if ($record->self_management_percent !== null)
                <div class="rounded-xl bg-white px-2.5 py-2 ring-1 ring-slate-100">
                    <p class="text-[9px] font-bold uppercase text-slate-400">Self management</p>
                    <div class="mt-1 flex items-end justify-between gap-1">
                        <span class="text-sm font-bold text-slate-900">{{ $record->self_management_percent }}%</span>
                        <x-monitoring.score-badge :label="$record->self_management_score_label" />
                    </div>
                </div>
            @endif
            @if ($record->medication_compliance_percent !== null)
                <div class="rounded-xl bg-white px-2.5 py-2 ring-1 ring-slate-100">
                    <p class="text-[9px] font-bold uppercase text-slate-400">Obat</p>
                    <div class="mt-1 flex items-end justify-between gap-1">
                        <span class="text-sm font-bold text-slate-900">{{ $record->medication_compliance_percent }}%</span>
                        <x-monitoring.score-badge :label="$record->medication_compliance_label" />
                    </div>
                </div>
            @endif
            @if ($record->relapse_score_label)
                <div class="rounded-xl bg-white px-2.5 py-2 ring-1 ring-slate-100">
                    <p class="text-[9px] font-bold uppercase text-slate-400">Kambuh</p>
                    <div class="mt-1 flex items-end justify-between gap-1">
                        <span class="text-sm font-bold text-slate-900">{{ $record->relapse_score }}</span>
                        <x-monitoring.score-badge :label="$record->relapse_score_label" />
                    </div>
                </div>
            @endif
        </div>
    @endif

    <div class="space-y-1.5 px-4 pb-4 pt-2">
        @if ($record->isDaily() && count($record->medicationBreakdown()) > 0)
            <div class="space-y-1">
                @foreach ($record->medicationBreakdown() as $med)
                    <p class="text-[11px] text-slate-600">
                        💊 <strong>{{ $med['name'] }}</strong>
                        · {{ $med['on_time'] ? 'Tepat waktu' : 'Belum / terlewat' }}
                        @if ($med['dose'] || $med['schedule'])
                            <span class="text-slate-400">({{ collect([$med['dose'], $med['schedule']])->filter()->implode(' · ') }})</span>
                        @endif
                        @if ($med['notes'])
                            <span class="block text-[10px] italic text-slate-500">{{ $med['notes'] }}</span>
                        @endif
                    </p>
                @endforeach
            </div>
        @elseif ($record->medication_on_time !== null && $record->isDaily())
            <p class="text-[11px] text-slate-600">
                💊 Minum obat tepat waktu: <strong>{{ $record->medication_on_time ? 'Ya' : 'Belum' }}</strong>
            </p>
        @endif
        @php $vitals = app(\App\Services\MonitoringScoreService::class)->vitalsSummary($record); @endphp
        @if ($vitals)
            <p class="text-[11px] leading-relaxed text-slate-600">🩺 {{ $vitals }}</p>
        @endif
        @if ($record->notes)
            <p class="rounded-lg bg-slate-50 px-2.5 py-2 text-[11px] italic text-slate-500">{{ $record->notes }}</p>
        @endif
    </div>
</article>
