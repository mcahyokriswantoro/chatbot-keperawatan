@php
    $averageBars = $charts['averageBarItems'];
    $hasFilters = $filters['q'] || $filters['type'] || $filters['disease'];
    $periodActive = ($showMonthlyDetail ?? false) || (($charts['periodLabel'] ?? '14 hari') !== '14 hari');
@endphp

<section class="admin-dashboard-section mb-4">
    <div class="admin-dashboard-section__head">
        <h2 class="admin-dashboard-section__title">Diagram Monitoring</h2>
        @if ($hasFilters || $periodActive)
            <span class="text-[10px] font-semibold text-brand-600">Sesuai filter</span>
        @endif
    </div>
    <div class="grid grid-cols-3 gap-2 border-b border-slate-100 px-3 py-3">
        <div class="rounded-xl bg-violet-50 px-2 py-2 text-center ring-1 ring-violet-100">
            <p class="text-lg font-bold text-violet-600">{{ $charts['total'] }}</p>
            <p class="text-[9px] font-medium text-slate-500">Total</p>
        </div>
        <div class="rounded-xl bg-emerald-50 px-2 py-2 text-center ring-1 ring-emerald-100">
            <p class="text-lg font-bold text-emerald-600">{{ $charts['dailyCount'] }}</p>
            <p class="text-[9px] font-medium text-slate-500">Harian</p>
        </div>
        <div class="rounded-xl bg-sky-50 px-2 py-2 text-center ring-1 ring-sky-100">
            <p class="text-lg font-bold text-sky-600">{{ $charts['monthlyCount'] }}</p>
            <p class="text-[9px] font-medium text-slate-500">Bulanan</p>
        </div>
    </div>

    <div class="admin-dashboard-section__body">
        <x-admin.line-chart
            title="Entri monitoring per hari"
            :data="$charts['overTime']"
            color="#7c3aed"
            :meta="$charts['periodLabel'] ?? '14 hari'"
        />        <x-admin.donut-chart title="Jenis monitoring" :items="$charts['typeDonutItems']" />
        <x-admin.bar-chart title="Monitoring per penyakit" :items="$charts['diseaseBarItems']" />
        @if (count($averageBars['complaint']) > 0)
            <x-admin.bar-chart
                title="Rata-rata skor keluhan"
                :items="$averageBars['complaint']"
                :max="100"
                hint="Parameter: ≤25% Baik · ≤50% Cukup · >50% Kurang"
            />
            <x-admin.bar-chart
                title="Rata-rata self management"
                :items="$averageBars['selfManagement']"
                :max="100"
                hint="Parameter: ≥80% Baik · ≥60% Cukup · <60% Kurang"
            />
            <x-admin.bar-chart
                title="Rata-rata kepatuhan obat"
                :items="$averageBars['compliance']"
                :max="100"
                hint="Parameter: ≥80% Baik · ≥60% Cukup · <60% Kurang"
            />
        @endif
    </div>
</section>
