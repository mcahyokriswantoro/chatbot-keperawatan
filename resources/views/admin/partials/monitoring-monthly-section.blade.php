@php

    $showMonthlyDetail = $showMonthlyDetail ?? false;

    $overview = $monthlyOverview;

    $periodFrom = $periodFrom ?? '';

    $periodTo = $periodTo ?? '';

@endphp



<div class="admin-monthly-overview mb-4">

    <form method="GET" class="space-y-4">

        @if ($filters['q'] ?? '')

            <input type="hidden" name="q" value="{{ $filters['q'] }}">

        @endif

        @if ($filters['type'] ?? '')

            <input type="hidden" name="type" value="{{ $filters['type'] }}">

        @endif

        @if ($filters['disease'] ?? '')

            <input type="hidden" name="disease" value="{{ $filters['disease'] }}">

        @endif



        <div class="monitoring-card-hero monitoring-card-hero--monthly overflow-hidden rounded-2xl p-4 shadow-lg">

            <p class="monitoring-card-hero__eyebrow text-[10px] font-semibold uppercase tracking-wider">Monitoring periode</p>

            @if ($showMonthlyDetail && $overview)

                <h2 class="monitoring-card-hero__title mt-1 text-lg font-bold">📅 Ringkasan {{ $overview['period_label'] }}</h2>

            @else

                <h2 class="monitoring-card-hero__title mt-1 text-lg font-bold">📅 Ringkasan monitoring</h2>

            @endif

            <p class="monitoring-card-hero__label mt-1 text-[11px]">

                Agregat semua pengguna

                @if ($filters['disease'] ?? '')

                    · {{ $stats->diseaseLabel($filters['disease']) }}

                @endif

            </p>

            <div class="mt-3 grid grid-cols-2 gap-2">

                <div>

                    <label class="monitoring-card-hero__label text-[11px] font-medium">Dari tanggal</label>

                    <input

                        type="date"

                        name="period_from"

                        value="{{ $periodFrom }}"

                        class="monitoring-month-input"

                        required

                    >

                </div>

                <div>

                    <label class="monitoring-card-hero__label text-[11px] font-medium">Sampai tanggal</label>

                    <input

                        type="date"

                        name="period_to"

                        value="{{ $periodTo }}"

                        class="monitoring-month-input"

                        required

                    >

                </div>

            </div>

            <button type="submit" class="monitoring-btn-primary monitoring-btn-primary--monthly{{ $showMonthlyDetail ? ' monitoring-btn-primary--monthly-active' : '' }} mt-3 w-full rounded-xl px-4 py-2.5 text-sm font-semibold shadow-sm">

                {{ $showMonthlyDetail ? 'Perbarui ringkasan' : 'Tampilkan ringkasan' }}

            </button>

            @unless ($showMonthlyDetail)

                <p class="mt-3 text-[11px] leading-relaxed text-white/80">

                    Pilih rentang tanggal untuk menampilkan ringkasan keluhan, self management, kepatuhan obat, dan grafik harian.

                </p>

            @endunless

        </div>



        @if ($showMonthlyDetail && $overview)

            <div class="grid grid-cols-2 gap-2">

                <div class="rounded-xl bg-emerald-50 px-3 py-2.5 text-center ring-1 ring-emerald-100">

                    <p class="text-lg font-bold text-emerald-600">{{ $overview['daily_count'] }}</p>

                    <p class="text-[9px] font-medium text-slate-500">Catatan harian</p>

                </div>

                <div class="rounded-xl bg-sky-50 px-3 py-2.5 text-center ring-1 ring-sky-100">

                    <p class="text-lg font-bold text-sky-600">{{ $overview['monthly_count'] }}</p>

                    <p class="text-[9px] font-medium text-slate-500">Laporan bulanan</p>

                </div>

            </div>



            @if (count($overview['disease_summaries']) === 0)

                <div class="rounded-2xl border border-dashed border-amber-200 bg-amber-50/80 p-5 text-center">

                    <p class="text-2xl">📅</p>

                    <p class="mt-2 text-sm font-bold text-amber-900">Belum ada data</p>

                    <p class="mt-1 text-xs leading-relaxed text-amber-800">

                        Periode {{ $overview['period_label'] }} belum memiliki catatan monitoring harian atau bulanan.

                    </p>

                </div>

            @else

                @foreach ($overview['disease_summaries'] as $summary)

                    <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">

                        <div class="flex items-start justify-between gap-2">

                            <h3 class="text-sm font-bold text-slate-900">{{ $summary['icon'] }} {{ $summary['label'] }}</h3>

                            <span class="shrink-0 rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-600">

                                {{ $summary['monthly_count'] }} laporan bulanan

                            </span>

                        </div>



                        <div class="mt-3 grid grid-cols-2 gap-2">

                            <div class="flex items-center justify-between gap-2 rounded-xl bg-slate-50 px-3 py-2.5 ring-1 ring-slate-100">

                                <div class="min-w-0">

                                    <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Keluhan</p>

                                    <p class="mt-0.5 text-sm font-bold text-slate-900">{{ $summary['complaint_total'] ?? '—' }}</p>

                                </div>

                                @if ($summary['complaint_label'])

                                    <x-monitoring.score-badge :label="$summary['complaint_label']" />

                                @endif

                            </div>

                            <div class="flex items-center justify-between gap-2 rounded-xl bg-slate-50 px-3 py-2.5 ring-1 ring-slate-100">

                                <div class="min-w-0">

                                    <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Self management</p>

                                    <p class="mt-0.5 text-sm font-bold text-slate-900">

                                        {{ $summary['self_management_percent'] !== null ? $summary['self_management_percent'].'%' : '—' }}

                                    </p>

                                </div>

                                @if ($summary['self_management_label'])

                                    <x-monitoring.score-badge :label="$summary['self_management_label']" />

                                @endif

                            </div>

                            <div class="flex items-center justify-between gap-2 rounded-xl bg-slate-50 px-3 py-2.5 ring-1 ring-slate-100">

                                <div class="min-w-0">

                                    <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Minum obat</p>

                                    <p class="mt-0.5 text-sm font-bold text-slate-900">

                                        {{ $summary['medication_compliance_percent'] !== null ? $summary['medication_compliance_percent'].'%' : '—' }}

                                    </p>

                                </div>

                                @if ($summary['medication_compliance_label'])

                                    <x-monitoring.score-badge :label="$summary['medication_compliance_label']" />

                                @endif

                            </div>

                            <div class="flex items-center justify-between gap-2 rounded-xl bg-slate-50 px-3 py-2.5 ring-1 ring-slate-100">

                                <div class="min-w-0">

                                    <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Catatan harian</p>

                                    <p class="mt-0.5 text-sm font-bold text-slate-900">{{ $summary['daily_count'] }} hari</p>

                                </div>

                                @if ($summary['relapse_score_label'])

                                    <x-monitoring.score-badge :label="$summary['relapse_score_label']" />

                                @endif

                            </div>

                        </div>



                        @if ($summary['medication_days_recorded'] > 0 || ($summary['medication_expected_days'] ?? null))

                            <p class="mt-2 text-[11px] text-slate-500">

                                Obat tepat waktu:

                                {{ $summary['medication_days_on_time'] }}

                                @if ($summary['medication_expected_days'] ?? null)

                                    / {{ $summary['medication_expected_days'] }} hari resep

                                @else

                                    / {{ $summary['medication_days_recorded'] }} hari tercatat

                                @endif

                            </p>

                        @endif

                    </div>

                @endforeach



                @if (count($overview['chart_data']) > 0)

                    @php

                        $lineData = collect($overview['chart_data'])->map(fn ($row) => [

                            'date' => $row['date'],

                            'value' => $row['value'],

                        ])->all();

                    @endphp

                    <x-admin.line-chart

                        title="Catatan harian per tanggal"

                        :data="$lineData"

                        color="#0284c7"

                        :meta="$overview['period_label'].' · '.$overview['period_days'].' hari'"

                    />

                @endif

            @endif

        @endif

    </form>

</div>

