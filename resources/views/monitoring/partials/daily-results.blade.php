@props([
    'disease',
    'summary',
    'chartData',
])

@php
    $complaintMax = count(config("monitoring_complaints.{$disease}", [])) * 3;
    $showCharts = ! empty($chartData);
    $showSection = ($summary['has_data'] ?? false) || $showCharts;
@endphp

@if ($showSection)
    <div class="space-y-4 pt-2 pb-1 monitoring-daily-results-block">
        <section class="monitoring-daily-results overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-card">
            <div class="monitoring-daily-results__header border-b border-slate-200 px-4 py-3.5">
                <h2 class="text-sm font-bold text-slate-900">Hasil Monitoring Harian</h2>
                @if ($summary['has_today'] ?? false)
                    <p class="mt-0.5 text-[11px] text-slate-600">Catatan hari ini · {{ today()->translatedFormat('d M Y') }}</p>
                @elseif ($summary['is_latest_fallback'] ?? false)
                    <p class="mt-0.5 text-[11px] text-slate-600">Catatan terakhir · {{ $summary['recorded_at_label'] }}</p>
                @elseif ($summary['has_data'] ?? false)
                    <p class="mt-0.5 text-[11px] text-slate-600">{{ $summary['recorded_at_label'] }}</p>
                @else
                    <p class="mt-0.5 text-[11px] text-slate-600">Isi form di atas untuk mencatat kondisi Anda</p>
                @endif
            </div>

            <ul class="space-y-3 p-4 text-xs leading-relaxed text-slate-700">
                <li class="flex flex-wrap items-center justify-between gap-2 rounded-xl bg-slate-50 px-3 py-2.5 ring-1 ring-slate-100">
                    <span>
                        <strong>Keluhan</strong>,
                        skor =
                        @if ($summary['complaint_total'] !== null)
                            {{ $summary['complaint_total'] }}
                        @else
                            —
                        @endif
                    </span>
                    @if ($summary['complaint_label'])
                        <x-monitoring.score-badge :label="$summary['complaint_label']" />
                    @endif
                </li>

                <li class="flex flex-wrap items-center justify-between gap-2 rounded-xl bg-slate-50 px-3 py-2.5 ring-1 ring-slate-100">
                    <span>
                        <strong>Persentase kepatuhan minum obat</strong>
                        @if ($summary['medication_compliance_percent'] !== null)
                            = {{ $summary['medication_compliance_percent'] }}%
                        @else
                            = —
                        @endif
                        @if (! empty($summary['medication_expected_days']))
                            <span class="block text-[10px] font-normal text-slate-500">Berdasarkan durasi resep dokter</span>
                        @endif
                    </span>
                    @if ($summary['medication_compliance_label'])
                        <x-monitoring.score-badge :label="$summary['medication_compliance_label']" />
                    @endif
                </li>

                <li class="flex flex-wrap items-center justify-between gap-2 rounded-xl bg-slate-50 px-3 py-2.5 ring-1 ring-slate-100">
                    <span>
                        <strong>Self management</strong>,
                        skor =
                        @if ($summary['self_management_percent'] !== null)
                            {{ $summary['self_management_percent'] }}%
                        @else
                            —
                        @endif
                    </span>
                    @if ($summary['self_management_label'])
                        <x-monitoring.score-badge :label="$summary['self_management_label']" />
                    @endif
                </li>

                <li class="rounded-xl bg-emerald-50/70 px-3 py-2.5 ring-1 ring-emerald-100">
                    <p class="font-semibold text-emerald-900">Tanda vital</p>
                    @if ($summary['vitals_raw'])
                        <p class="mt-1.5 text-slate-700">{{ $summary['vitals_raw'] }}</p>
                        @if ($summary['vitals_narrative'])
                            <p class="mt-2 rounded-lg bg-white/80 px-2.5 py-2 text-[11px] leading-relaxed text-emerald-800 ring-1 ring-emerald-100">
                                💡 {{ $summary['vitals_narrative'] }}
                            </p>
                        @endif
                    @else
                        <p class="mt-1 text-slate-500">Belum diisi hari ini (opsional).</p>
                    @endif
                </li>
            </ul>
        </section>

        @if ($showCharts)
            <div class="space-y-2">
                <h2 class="text-sm font-bold text-slate-900">Grafik per hari</h2>
                <p class="text-[11px] text-slate-500">14 hari terakhir</p>
                <x-monitoring.mini-chart
                    title="Keluhan"
                    :data="$chartData"
                    field="complaint"
                    :max="$complaintMax"
                    color="brand"
                />
                <x-monitoring.mini-chart
                    title="Kepatuhan minum obat (%)"
                    :data="$chartData"
                    field="compliance"
                    :max="100"
                    color="violet"
                />
                <x-monitoring.mini-chart
                    title="Self management (%)"
                    :data="$chartData"
                    field="self_management"
                    :max="100"
                    color="emerald"
                />
            </div>
        @endif
    </div>
@endif
