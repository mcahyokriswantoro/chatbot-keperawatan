@props(['entry'])

@php
    $isMonthly = $entry->isMonthly();
    $accentBorder = $isMonthly ? 'border-l-sky-500' : 'border-l-emerald-500';
    $typeBadge = $isMonthly
        ? 'bg-sky-100 text-sky-800 ring-sky-200'
        : 'bg-emerald-100 text-emerald-800 ring-emerald-200';
@endphp

<a
    href="{{ route('admin.monitoring.show', $entry) }}"
    @class(['block overflow-hidden rounded-2xl border border-brand-100 border-l-4 bg-white shadow-sm transition active:scale-[0.99]', $accentBorder])
>
    <div class="flex items-start justify-between gap-3 p-4 pb-3">
        <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center gap-2">
                <p class="font-bold text-slate-900">{{ $entry->user?->name ?? 'Pengguna' }}</p>
                <span @class(['rounded-full px-2 py-0.5 text-[10px] font-bold uppercase ring-1', $typeBadge])>
                    {{ $entry->monitorTypeLabel() }}
                </span>
            </div>
            <p class="mt-0.5 text-[11px] text-slate-400">
                {{ ($entry->recorded_at ?? $entry->created_at)->translatedFormat('d M Y') }}
                @if ($entry->user?->email)
                    · {{ $entry->user->email }}
                @endif
            </p>
            @if ($entry->diseaseLabel())
                <p class="mt-1 text-xs font-medium text-brand-700">{{ $entry->diseaseLabel() }}</p>
            @endif
            @if ($isMonthly && $entry->period_month)
                <p class="mt-0.5 text-[10px] text-slate-400">Periode {{ $entry->period_month }}</p>
            @endif
        </div>
        <svg class="h-5 w-5 shrink-0 text-brand-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
    </div>

    @if ($entry->complaint_total !== null || $entry->complaint_score_label || $entry->self_management_percent !== null || $entry->medication_compliance_percent !== null || $entry->relapse_score_label)
        <div class="grid grid-cols-2 gap-2 border-t border-slate-100 bg-slate-50/50 px-4 py-3">
            @if ($entry->complaint_total !== null || $entry->complaint_score_label)
                <div class="rounded-xl bg-white px-2.5 py-2 ring-1 ring-slate-100">
                    <p class="text-[9px] font-bold uppercase text-slate-400">Gejala</p>
                    <div class="mt-1 flex items-end justify-between gap-1">
                        <span class="text-sm font-bold text-slate-900">{{ $entry->complaint_total ?? '—' }}</span>
                        <x-monitoring.score-badge :label="$entry->complaint_score_label" />
                    </div>
                </div>
            @endif
            @if ($entry->self_management_percent !== null)
                <div class="rounded-xl bg-white px-2.5 py-2 ring-1 ring-slate-100">
                    <p class="text-[9px] font-bold uppercase text-slate-400">Perawatan mandiri</p>
                    <div class="mt-1 flex items-end justify-between gap-1">
                        <span class="text-sm font-bold text-slate-900">{{ $entry->self_management_percent }}%</span>
                        <x-monitoring.score-badge :label="$entry->self_management_score_label" />
                    </div>
                </div>
            @endif
            @if ($entry->medication_compliance_percent !== null)
                <div class="rounded-xl bg-white px-2.5 py-2 ring-1 ring-slate-100">
                    <p class="text-[9px] font-bold uppercase text-slate-400">Obat</p>
                    <div class="mt-1 flex items-end justify-between gap-1">
                        <span class="text-sm font-bold text-slate-900">{{ $entry->medication_compliance_percent }}%</span>
                        <x-monitoring.score-badge :label="$entry->medication_compliance_label" />
                    </div>
                </div>
            @endif
            @if ($entry->relapse_score_label)
                <div class="rounded-xl bg-white px-2.5 py-2 ring-1 ring-slate-100">
                    <p class="text-[9px] font-bold uppercase text-slate-400">Kambuh</p>
                    <div class="mt-1 flex items-end justify-between gap-1">
                        <span class="text-sm font-bold text-slate-900">{{ $entry->relapse_score }}</span>
                        <x-monitoring.score-badge :label="$entry->relapse_score_label" />
                    </div>
                </div>
            @endif
        </div>
    @endif

    <div class="space-y-1 px-4 pb-4 pt-2">
        @if ($entry->isDaily() && count($entry->medicationBreakdown()) > 0)
            @foreach ($entry->medicationBreakdown() as $med)
                <p class="text-[11px] text-slate-600">
                    💊 {{ $med['name'] }}: <strong>{{ $med['on_time'] ? 'Tepat waktu' : 'Belum' }}</strong>
                </p>
            @endforeach
        @elseif ($entry->medication_on_time !== null && $entry->isDaily())
            <p class="text-[11px] text-slate-600">
                💊 Minum obat tepat waktu: <strong>{{ $entry->medication_on_time ? 'Ya' : 'Belum' }}</strong>
            </p>
        @endif
        @if ($entry->vitalsSummary())
            <p class="text-[11px] leading-relaxed text-slate-600">🩺 {{ $entry->vitalsSummary() }}</p>
        @endif
        @if ($entry->complaints && ! $entry->complaint_answers)
            <p class="text-[11px] leading-relaxed text-slate-600">{{ Str::limit($entry->complaints, 100) }}</p>
        @endif
    </div>
</a>
