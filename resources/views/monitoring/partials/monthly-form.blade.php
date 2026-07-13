@props([
    'disease',
    'diseaseInfo',
    'preview',
    'relapseOptions',
    'currentMonth',
])

@php
    $inputClass = 'mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2.5 text-sm focus:border-brand-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-100';
    $scoreLabels = config('monitoring.score_labels', []);
    $vitalsStarted = filled(old('systolic'))
        || filled(old('diastolic'))
        || filled(old('heart_rate'))
        || filled(old('temperature'))
        || filled(old('respiratory_rate'))
        || filled(old('blood_sugar'))
        || filled(old('oxygen_saturation'))
        || filled(old('weight'));
    $relapseStarted = filled(old('relapse_frequency'));
    $relapseLabels = config('monitoring.relapse_labels', []);
    $initialRelapseFrequency = old('relapse_frequency', $preview['relapse_frequency'] ?? null);
    $complaintMax = count(config("monitoring_complaints.{$disease}", [])) * 3;
@endphp

<form
    method="POST"
    action="{{ route('monitoring.store') }}"
    class="space-y-4"
    novalidate
    x-data="{
        preview: @js($preview),
        loading: false,
        month: @js(old('period_month', $currentMonth)),
        scoreLabels: @js($scoreLabels),
        vitalsStarted: @js($vitalsStarted),
        relapseStarted: @js($relapseStarted),
        relapseOptions: @js($relapseOptions),
        relapseLabels: @js($relapseLabels),
        relapseFrequency: @js($initialRelapseFrequency),
        relapseScore: @js($preview['relapse_score'] ?? null),
        relapseScoreLabel: @js($preview['relapse_score_label'] ?? null),
        relapseChoiceLabel: null,
        updateRelapse(value) {
            if (! value) {
                this.relapseScore = null;
                this.relapseScoreLabel = null;
                this.relapseChoiceLabel = null;
                return;
            }
            this.relapseFrequency = value;
            const opt = this.relapseOptions.find(o => String(o.value) === String(value));
            if (! opt) {
                this.relapseScore = null;
                this.relapseScoreLabel = null;
                this.relapseChoiceLabel = null;
                return;
            }
            this.relapseScore = opt.score;
            this.relapseScoreLabel = this.relapseLabels[opt.score] ?? this.relapseLabels[String(opt.score)] ?? 'kurang';
            this.relapseChoiceLabel = opt.label;
        },
        syncRelapseFromPreview() {
            if (this.preview?.relapse_frequency) {
                this.updateRelapse(this.preview.relapse_frequency);
            }
        },
        get relapseSummaryText() {
            if (! this.relapseScoreLabel) {
                return 'Frekuensi kekambuhan — belum diisi';
            }

            return 'Frekuensi kekambuhan, skor = ' + this.relapseScore;
        },
        labelText(key) {
            if (! key) return '';
            return this.scoreLabels[key] ?? key.charAt(0).toUpperCase() + key.slice(1);
        },
        badgeClass(key) {
            const map = {
                baik: 'bg-emerald-100 text-emerald-800 ring-emerald-200',
                cukup: 'bg-amber-100 text-amber-800 ring-amber-200',
                kurang: 'bg-rose-100 text-rose-800 ring-rose-200',
            };
            return map[key] ?? 'bg-slate-100 text-slate-700 ring-slate-200';
        },
        async loadPreview() {
            this.loading = true;
            try {
                const url = new URL(@js(route('monitoring.preview')));
                url.searchParams.set('disease', @js($disease));
                url.searchParams.set('month', this.month);
                const res = await fetch(url);
                if (res.ok) {
                    this.preview = await res.json();
                    this.syncRelapseFromPreview();
                }
            } finally {
                this.loading = false;
            }
        },
        get hasDailyData() {
            return (this.preview?.daily_count ?? 0) > 0;
        },
        chartYMax(field, max) {
            if (field === 'compliance' || field === 'self_management') {
                return 100;
            }

            return Math.max(1, Number(max) || 1);
        },
        chartPlotLeft(index) {
            const count = this.preview?.chart_data?.length ?? 0;
            if (count <= 1) {
                return 50;
            }

            return (index / (count - 1)) * 100;
        },
        chartPlotBottom(field, value, max) {
            if (value === null || value === undefined) {
                return 0;
            }

            const yMax = this.chartYMax(field, max);

            return Math.min(100, Math.max(0, (Number(value) / yMax) * 100));
        },
        chartGridSvgY(tick, field, max) {
            const yMax = this.chartYMax(field, max);

            return 100 - ((tick / yMax) * 100);
        },
        chartLinePointsSvg(field, max) {
            const data = this.preview?.chart_data ?? [];

            return data.map((point, index) => {
                const x = this.chartPlotLeft(index);
                const y = 100 - this.chartPlotBottom(field, point[field], max);

                return `${x},${y}`;
            }).join(' ');
        },
        chartAreaPath(field, max) {
            const data = this.preview?.chart_data ?? [];
            if (data.length < 2) {
                return '';
            }

            const line = data.map((point, index) => {
                const x = this.chartPlotLeft(index);
                const y = 100 - this.chartPlotBottom(field, point[field], max);

                return `${x},${y}`;
            }).join(' L ');

            const lastX = this.chartPlotLeft(data.length - 1);
            const firstX = this.chartPlotLeft(0);

            return `M ${line} L ${lastX},100 L ${firstX},100 Z`;
        },
        chartYTicks(field, max) {
            const yMax = this.chartYMax(field, max);
            const steps = 4;
            const ticks = [];

            for (let i = steps; i >= 0; i--) {
                ticks.push(Math.round((yMax * i) / steps));
            }

            return ticks;
        },
        chartFill(color) {
            const map = {
                brand: 'rgba(0, 102, 255, 0.12)',
                violet: 'rgba(124, 58, 237, 0.12)',
                emerald: 'rgba(5, 150, 105, 0.12)',
            };

            return map[color] ?? map.brand;
        },
        chartStroke(color) {
            const map = {
                brand: '#0066ff',
                violet: '#7c3aed',
                emerald: '#059669',
            };

            return map[color] ?? map.brand;
        },
    }"
    x-init="
        syncRelapseFromPreview();
        if (relapseFrequency && ! relapseScoreLabel) { updateRelapse(relapseFrequency); }
        $watch('relapseFrequency', value => { if (value) updateRelapse(value); });
        if (month !== @js($currentMonth)) { loadPreview(); }
    "
>
    @csrf
    <input type="hidden" name="monitor_type" value="monthly">
    <input type="hidden" name="disease" value="{{ $disease }}">

    <div class="monitoring-card-hero monitoring-card-hero--monthly overflow-hidden rounded-2xl p-4 shadow-lg">
        <p class="monitoring-card-hero__eyebrow text-[10px] font-semibold uppercase tracking-wider">Ringkasan bulan ini</p>
        <h2 class="monitoring-card-hero__title mt-1 text-lg font-bold">{{ $diseaseInfo['icon'] }} {{ $diseaseInfo['label'] }}</h2>
        <div class="mt-3">
            <label class="monitoring-card-hero__label text-[11px] font-medium">Pilih bulan</label>
            <input
                type="month"
                name="period_month"
                x-model="month"
                @change="loadPreview()"
                required
                class="monitoring-month-input"
            >
        </div>
    </div>

    <div x-show="loading" x-cloak class="rounded-xl bg-slate-50 px-4 py-3 text-center text-xs text-slate-500">
        Memuat data bulan…
    </div>

    <div x-show="!loading && !hasDailyData" x-cloak class="rounded-2xl border border-dashed border-amber-200 bg-amber-50/80 p-5 text-center">
        <p class="text-2xl">📅</p>
        <p class="mt-2 text-sm font-bold text-amber-900">Belum ada catatan harian</p>
        <p class="mt-1 text-xs leading-relaxed text-amber-800">
            Bulan <span x-text="month"></span> belum memiliki data monitoring harian.
            Isi tab <strong>Harian</strong> dulu — ringkasan bulanan akan otomatis terisi.
        </p>
    </div>

    <template x-if="!loading && hasDailyData">
        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-2">
                <div class="flex items-center justify-between gap-3 rounded-xl bg-slate-50 px-3 py-2.5 ring-1 ring-slate-100">
                    <div class="min-w-0">
                        <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Keluhan</p>
                        <p class="mt-0.5 text-sm font-bold text-slate-900" x-text="preview.complaint_total ?? '—'"></p>
                    </div>
                    <span
                        x-show="preview.complaint_label"
                        x-text="labelText(preview.complaint_label)"
                        :class="'inline-flex shrink-0 rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide ring-1 ' + badgeClass(preview.complaint_label)"
                    ></span>
                </div>
                <div class="flex items-center justify-between gap-3 rounded-xl bg-slate-50 px-3 py-2.5 ring-1 ring-slate-100">
                    <div class="min-w-0">
                        <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Self management</p>
                        <p class="mt-0.5 text-sm font-bold text-slate-900" x-text="preview.self_management_percent !== null ? preview.self_management_percent + '%' : '—'"></p>
                    </div>
                    <span
                        x-show="preview.self_management_label"
                        x-text="labelText(preview.self_management_label)"
                        :class="'inline-flex shrink-0 rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide ring-1 ' + badgeClass(preview.self_management_label)"
                    ></span>
                </div>
                <div class="flex items-center justify-between gap-3 rounded-xl bg-slate-50 px-3 py-2.5 ring-1 ring-slate-100">
                    <div class="min-w-0">
                        <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Minum obat</p>
                        <p class="mt-0.5 text-sm font-bold text-slate-900" x-text="preview.medication_compliance_percent !== null ? preview.medication_compliance_percent + '%' : '—'"></p>
                    </div>
                    <span
                        x-show="preview.medication_compliance_label"
                        x-text="labelText(preview.medication_compliance_label)"
                        :class="'inline-flex shrink-0 rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide ring-1 ' + badgeClass(preview.medication_compliance_label)"
                    ></span>
                </div>
                <div class="flex items-center justify-between gap-3 rounded-xl bg-slate-50 px-3 py-2.5 ring-1 ring-slate-100">
                    <div class="min-w-0">
                        <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Catatan harian</p>
                        <p class="mt-0.5 text-sm font-bold text-slate-900" x-text="preview.daily_count + ' hari'"></p>
                    </div>
                </div>
            </div>

            <x-monitoring.section letter="a" title="Keluhan sebulan terakhir" subtitle="Kami rangkum dari catatan harian Anda" accent="rose">
                <div class="flex flex-wrap items-center justify-between gap-2 rounded-xl bg-rose-50/60 px-3 py-2.5 ring-1 ring-rose-100">
                    <span class="text-xs text-slate-700">
                        <strong>Keluhan</strong>, skor =
                        <span x-text="preview.complaint_total ?? '—'" class="font-bold text-slate-900"></span>
                    </span>
                    <span
                        x-show="preview.complaint_label"
                        x-text="labelText(preview.complaint_label)"
                        :class="'inline-flex shrink-0 rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide ring-1 ' + badgeClass(preview.complaint_label)"
                    ></span>
                </div>
                <p class="mt-3 text-xs leading-relaxed text-slate-600">
                    Berdasarkan <span x-text="preview.daily_count"></span> hari yang Anda catat pada bulan ini.
                </p>
            </x-monitoring.section>

            @include('monitoring.partials.relapse-section', [
                'relapseOptions' => $relapseOptions,
                'savedRelapseFrequency' => $preview['relapse_frequency'] ?? null,
            ])

            <x-monitoring.section letter="c" title="Minum obat tepat waktu" subtitle="Persentase = hari tepat waktu ÷ durasi resep dokter" accent="violet">
                <dl class="space-y-2">
                    <template x-if="preview.prescription_days">
                        <div class="flex justify-between rounded-lg bg-violet-50/60 px-3 py-2 text-sm">
                            <dt class="text-slate-600">Durasi resep dokter</dt>
                            <dd class="font-bold text-slate-900" x-text="preview.prescription_days + ' hari'"></dd>
                        </div>
                    </template>
                    <div class="flex justify-between rounded-lg bg-violet-50/60 px-3 py-2 text-sm">
                        <dt class="text-slate-600">Hari minum obat tepat waktu</dt>
                        <dd class="font-bold text-slate-900">
                            <span x-text="preview.medication_days_on_time"></span>
                            <template x-if="preview.medication_expected_days">
                                <span x-text="'/' + preview.medication_expected_days + ' hari resep'"></span>
                            </template>
                            <template x-if="!preview.medication_expected_days">
                                <span x-text="'/' + preview.medication_days_recorded + ' hari tercatat'"></span>
                            </template>
                        </dd>
                    </div>
                    <p class="text-[11px] leading-relaxed text-slate-500">
                        Bulan ini Anda catat obat
                        <span class="font-semibold text-slate-700" x-text="preview.medication_days_recorded_in_month ?? 0"></span>
                        hari. Hari tanpa catatan dihitung belum patuh jika durasi resep sudah diisi.
                    </p>
                </dl>
            </x-monitoring.section>

            @include('monitoring.partials.monthly-vitals-section', [
                'inputClass' => $inputClass,
            ])

            <div class="overflow-hidden rounded-2xl border border-brand-200 bg-gradient-to-br from-brand-50 to-white p-4 shadow-card">
                <h2 class="text-sm font-bold text-slate-900">Ringkasan bulan ini</h2>
                <div class="mt-3 grid gap-2">
                    <div class="flex items-center justify-between rounded-xl bg-white px-3 py-2 ring-1 ring-slate-100">
                        <span class="text-xs text-slate-600">Keluhan</span>
                        <span
                            x-show="preview.complaint_label"
                            x-text="labelText(preview.complaint_label)"
                            :class="'inline-flex shrink-0 rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide ring-1 ' + badgeClass(preview.complaint_label)"
                        ></span>
                    </div>
                    <div class="flex flex-wrap items-center justify-between gap-2 rounded-xl bg-white px-3 py-2 ring-1 ring-slate-100">
                        <span class="text-xs text-slate-600" x-text="relapseSummaryText"></span>
                        <span
                            x-show="relapseScoreLabel"
                            x-cloak
                            x-text="labelText(relapseScoreLabel)"
                            :class="'inline-flex shrink-0 rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide ring-1 ' + badgeClass(relapseScoreLabel)"
                        ></span>
                    </div>
                    <div class="flex items-center justify-between rounded-xl bg-white px-3 py-2 ring-1 ring-slate-100">
                        <span class="text-xs text-slate-600">Minum obat</span>
                        <span
                            x-show="preview.medication_compliance_label"
                            x-text="labelText(preview.medication_compliance_label)"
                            :class="'inline-flex shrink-0 rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide ring-1 ' + badgeClass(preview.medication_compliance_label)"
                        ></span>
                    </div>
                    <div class="flex items-center justify-between rounded-xl bg-white px-3 py-2 ring-1 ring-slate-100">
                        <span class="text-xs text-slate-600">Self management</span>
                        <span
                            x-show="preview.self_management_label"
                            x-text="labelText(preview.self_management_label)"
                            :class="'inline-flex shrink-0 rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide ring-1 ' + badgeClass(preview.self_management_label)"
                        ></span>
                    </div>
                </div>
                <textarea name="notes" rows="2" class="mt-3 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm" placeholder="Ada hal lain yang ingin Anda ceritakan bulan ini? (opsional)">{{ old('notes') }}</textarea>
                <x-monitoring.section-save section="notes" label="Simpan catatan" ready="true" />
            </div>

            @include('monitoring.partials.monthly-charts', [
                'complaintMax' => $complaintMax,
            ])
        </div>
    </template>
</form>
