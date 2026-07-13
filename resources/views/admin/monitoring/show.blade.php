@extends('layouts.admin')

@section('title', 'Detail Monitoring')

@section('content')
    @php
        $isMonthly = $entry->isMonthly();
        $typeBadge = $isMonthly
            ? 'bg-sky-100 text-sky-800 ring-sky-200'
            : 'bg-emerald-100 text-emerald-800 ring-emerald-200';
    @endphp

    <x-admin.page-banner
        :title="$entry->diseaseLabel() ?? 'Monitoring'"
        :subtitle="($entry->recorded_at ?? $entry->created_at)->translatedFormat('d F Y')"
        :back="route('admin.monitoring.index')"
        tone="violet"
        :show-actions="false"
    />

    {{-- Ringkasan --}}
    <section class="mb-5 overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-sm">
        <div class="border-b border-brand-50 bg-violet-50/60 px-4 py-4">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span @class(['rounded-full px-2.5 py-1 text-[10px] font-bold uppercase ring-1', $typeBadge])>
                            {{ $entry->monitorTypeLabel() }}
                        </span>
                        @if ($isMonthly && $entry->period_month)
                            <span class="rounded-full bg-white px-2.5 py-1 text-[10px] font-semibold text-slate-600 ring-1 ring-slate-200">
                                Periode {{ $entry->period_month }}
                            </span>
                        @endif
                    </div>
                    <p class="mt-2 text-sm font-bold text-slate-900">{{ $entry->diseaseLabel() ?? '—' }}</p>
                    <p class="mt-0.5 text-xs text-slate-500">
                        Risiko self-management: <strong>{{ $entry->userRiskLevel() }}</strong>
                    </p>
                </div>
            </div>

            @if ($entry->complaint_total !== null || $entry->self_management_percent !== null || $entry->medication_compliance_percent !== null || $entry->relapse_score_label)
                <div class="mt-4 grid grid-cols-2 gap-2">
                    @if ($entry->complaint_total !== null || $entry->complaint_score_label)
                        <div class="rounded-xl bg-white px-3 py-2.5 ring-1 ring-slate-100">
                            <p class="text-[9px] font-bold uppercase text-slate-400">Gejala</p>
                            <div class="mt-1 flex items-end justify-between gap-1">
                                <span class="text-lg font-bold text-slate-900">{{ $entry->complaint_total ?? '—' }}</span>
                                <x-monitoring.score-badge :label="$entry->complaint_score_label" />
                            </div>
                        </div>
                    @endif
                    @if ($entry->self_management_percent !== null)
                        <div class="rounded-xl bg-white px-3 py-2.5 ring-1 ring-slate-100">
                            <p class="text-[9px] font-bold uppercase text-slate-400">Perawatan mandiri</p>
                            <div class="mt-1 flex items-end justify-between gap-1">
                                <span class="text-lg font-bold text-slate-900">{{ $entry->self_management_percent }}%</span>
                                <x-monitoring.score-badge :label="$entry->self_management_score_label" />
                            </div>
                        </div>
                    @endif
                    @if ($entry->medication_compliance_percent !== null)
                        <div class="rounded-xl bg-white px-3 py-2.5 ring-1 ring-slate-100">
                            <p class="text-[9px] font-bold uppercase text-slate-400">Kepatuhan obat</p>
                            <div class="mt-1 flex items-end justify-between gap-1">
                                <span class="text-lg font-bold text-slate-900">{{ $entry->medication_compliance_percent }}%</span>
                                <x-monitoring.score-badge :label="$entry->medication_compliance_label" />
                            </div>
                        </div>
                    @endif
                    @if ($entry->relapse_score_label)
                        <div class="rounded-xl bg-white px-3 py-2.5 ring-1 ring-slate-100">
                            <p class="text-[9px] font-bold uppercase text-slate-400">Frekuensi kambuh</p>
                            <div class="mt-1 flex items-end justify-between gap-1">
                                <span class="text-lg font-bold text-slate-900">{{ $entry->relapse_score }}</span>
                                <x-monitoring.score-badge :label="$entry->relapse_score_label" />
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </section>

    {{-- Gejala harian --}}
    @if ($complaintRows !== [])
        <section class="mb-5 rounded-2xl border border-brand-100 bg-white p-4 shadow-sm">
            <h2 class="text-sm font-bold text-slate-900">Gejala</h2>
            <p class="mt-0.5 text-xs text-slate-500">Jawaban keluhan per gejala</p>
            <dl class="mt-3 space-y-2">
                @foreach ($complaintRows as $row)
                    <div class="rounded-xl bg-rose-50/40 px-3 py-2.5 ring-1 ring-rose-100/80">
                        <dt class="text-xs leading-relaxed text-slate-800">{{ $row['question'] }}</dt>
                        <dd class="mt-1 text-sm font-bold text-slate-900">{{ $row['answer'] }}</dd>
                    </div>
                @endforeach
            </dl>
        </section>
    @elseif ($entry->complaints)
        <section class="mb-5 rounded-2xl border border-brand-100 bg-white p-4 shadow-sm">
            <h2 class="text-sm font-bold text-slate-900">Keluhan (format lama)</h2>
            <p class="mt-2 text-sm leading-relaxed text-slate-700 whitespace-pre-wrap">{{ $entry->complaints }}</p>
        </section>
    @endif

    {{-- Obat --}}
    @if (count($entry->medicationBreakdown()) > 0 || $entry->medication_compliance_percent !== null)
        <section class="mb-5 rounded-2xl border border-brand-100 bg-white p-4 shadow-sm">
            <h2 class="text-sm font-bold text-slate-900">Obat</h2>

            @if (count($entry->medicationBreakdown()) > 0)
                <div class="mt-3 space-y-3">
                    @foreach ($entry->medicationBreakdown() as $med)
                        <div class="rounded-xl bg-violet-50/70 px-3 py-3 ring-1 ring-violet-100">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-bold text-slate-900">{{ $med['name'] }}</p>
                                    @if ($med['dose'] || $med['schedule'])
                                        <p class="mt-0.5 text-xs text-violet-800">{{ collect([$med['dose'], $med['schedule']])->filter()->implode(' · ') }}</p>
                                    @endif
                                </div>
                                @if ($entry->isDaily())
                                    <span class="shrink-0 rounded-full px-2 py-0.5 text-[10px] font-bold {{ $med['on_time'] ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ $med['on_time'] ? 'Tepat waktu' : 'Belum' }}
                                    </span>
                                @endif
                            </div>
                            @if ($med['notes'])
                                <p class="mt-2 text-[11px] italic text-slate-600">{{ $med['notes'] }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            @if ($entry->medication_compliance_percent !== null)
                <dl class="mt-3 space-y-2 border-t border-slate-100 pt-3 text-sm">
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500">Kepatuhan bulanan</dt>
                        <dd class="flex items-center gap-2 font-bold">
                            {{ $entry->medication_compliance_percent }}%
                            <x-monitoring.score-badge :label="$entry->medication_compliance_label" />
                        </dd>
                    </div>
                </dl>
            @endif
        </section>
    @elseif ($entry->medication_name || $entry->medication_on_time !== null || $entry->medication_prescription_days)
        <section class="mb-5 rounded-2xl border border-brand-100 bg-white p-4 shadow-sm">
            <h2 class="text-sm font-bold text-slate-900">Obat</h2>
            <dl class="mt-3 space-y-2 text-sm">
                @if ($entry->medication_name)
                    <div class="flex justify-between gap-4 border-b border-slate-50 pb-2">
                        <dt class="text-slate-500">Nama obat</dt>
                        <dd class="text-right font-medium">{{ $entry->medication_name }}</dd>
                    </div>
                @endif
                @if ($entry->medication_on_time !== null && $entry->isDaily())
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500">Minum tepat waktu</dt>
                        <dd class="font-bold {{ $entry->medication_on_time ? 'text-emerald-700' : 'text-amber-700' }}">
                            {{ $entry->medication_on_time ? 'Ya' : 'Belum' }}
                        </dd>
                    </div>
                @endif
            </dl>
        </section>
    @endif

    {{-- Kekambuhan (bulanan) --}}
    @if ($entry->relapse_frequency)
        <section class="mb-5 rounded-2xl border border-brand-100 bg-white p-4 shadow-sm">
            <h2 class="text-sm font-bold text-slate-900">Frekuensi kekambuhan</h2>
            <p class="mt-2 text-sm font-medium text-slate-900">{{ $entry->relapseFrequencyLabel() }}</p>
            @if ($entry->relapse_score !== null)
                <p class="mt-1 text-xs text-slate-500">Skor: {{ $entry->relapse_score }} · {{ $entry->displayRelapseLabel() }}</p>
            @endif
        </section>
    @endif

    {{-- Perawatan mandiri --}}
    @if ($selfManagementRows !== [])
        <section class="mb-5 rounded-2xl border border-brand-100 bg-white p-4 shadow-sm">
            <h2 class="text-sm font-bold text-slate-900">Perawatan mandiri</h2>
            <p class="mt-0.5 text-xs text-slate-500">Evaluasi harian (risiko {{ $entry->userRiskLevel() }})</p>
            <dl class="mt-3 space-y-2">
                @foreach ($selfManagementRows as $row)
                    <div class="rounded-xl bg-amber-50/40 px-3 py-2.5 ring-1 ring-amber-100/80">
                        <dt class="text-xs leading-relaxed text-slate-800">{{ $row['question'] }}</dt>
                        <dd class="mt-1 text-sm font-bold text-slate-900">{{ $row['answer'] }}</dd>
                    </div>
                @endforeach
            </dl>
        </section>
    @elseif ($entry->activities)
        <section class="mb-5 rounded-2xl border border-brand-100 bg-white p-4 shadow-sm">
            <h2 class="text-sm font-bold text-slate-900">Aktivitas (format lama)</h2>
            <p class="mt-2 text-sm leading-relaxed text-slate-700 whitespace-pre-wrap">{{ $entry->activities }}</p>
        </section>
    @endif

    {{-- Tanda vital --}}
    <section class="mb-5 rounded-2xl border border-brand-100 bg-white p-4 shadow-sm">
        <h2 class="text-sm font-bold text-slate-900">Tanda vital</h2>
        @if ($entry->vitalsSummary())
            <p class="mt-2 rounded-xl bg-emerald-50 px-3 py-2 text-xs text-emerald-900 ring-1 ring-emerald-100">{{ $entry->vitalsSummary() }}</p>
        @endif
        <dl class="mt-3 space-y-3 text-sm">
            <div class="flex justify-between gap-4 border-b border-slate-50 pb-2"><dt class="text-slate-500">Tekanan darah</dt><dd class="font-bold text-brand-700">{{ $entry->bloodPressureLabel() ?? '—' }}</dd></div>
            <div class="flex justify-between gap-4 border-b border-slate-50 pb-2"><dt class="text-slate-500">Nadi</dt><dd class="font-medium">{{ $entry->heart_rate ? $entry->heart_rate.' bpm' : '—' }}</dd></div>
            <div class="flex justify-between gap-4 border-b border-slate-50 pb-2"><dt class="text-slate-500">Suhu</dt><dd class="font-medium">{{ $entry->temperature ? $entry->temperature.' °C' : '—' }}</dd></div>
            <div class="flex justify-between gap-4 border-b border-slate-50 pb-2"><dt class="text-slate-500">Laju napas</dt><dd class="font-medium">{{ $entry->respiratory_rate ? $entry->respiratory_rate.'/menit' : '—' }}</dd></div>
            <div class="flex justify-between gap-4 border-b border-slate-50 pb-2"><dt class="text-slate-500">SpO₂</dt><dd class="font-medium">{{ $entry->oxygen_saturation ? $entry->oxygen_saturation.'%' : '—' }}</dd></div>
            <div class="flex justify-between gap-4 border-b border-slate-50 pb-2"><dt class="text-slate-500">Gula darah</dt><dd class="font-medium">{{ $entry->blood_sugar ? $entry->blood_sugar.' mg/dL' : '—' }}</dd></div>
            <div class="flex justify-between gap-4"><dt class="text-slate-500">Berat badan</dt><dd class="font-medium">{{ $entry->weight ? $entry->weight.' kg' : '—' }}</dd></div>
        </dl>
    </section>

    @if ($entry->notes)
        <section class="mb-5 rounded-2xl border border-brand-100 bg-white p-4 shadow-sm">
            <h2 class="text-sm font-bold text-slate-900">Catatan</h2>
            <p class="mt-2 text-sm leading-relaxed text-slate-700 whitespace-pre-wrap">{{ $entry->notes }}</p>
        </section>
    @endif

    @if ($entry->user)
        <section class="rounded-2xl border border-brand-100 bg-white p-4 shadow-sm">
            <p class="text-[10px] font-semibold uppercase text-slate-500">Pengguna</p>
            <p class="mt-1 font-bold text-slate-900">{{ $entry->user->name }}</p>
            <p class="text-xs text-slate-500">{{ $entry->user->email }}</p>
            <a href="{{ route('admin.users.show', $entry->user) }}" class="mt-3 inline-block rounded-full bg-brand-600 px-4 py-2 text-xs font-semibold text-white">Profil pengguna</a>
        </section>
    @endif
@endsection
