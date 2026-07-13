@php
    $hasFilters = $filters['disease'] || $filters['risk'] || $filters['q'];
@endphp

<section class="admin-dashboard-section mb-4">
    <div class="admin-dashboard-section__head">
        <h2 class="admin-dashboard-section__title">Diagram Hasil Skrining</h2>
        @if ($hasFilters)
            <span class="text-[10px] font-semibold text-brand-600">Sesuai filter</span>
        @endif
    </div>

    <div class="grid grid-cols-3 gap-2 border-b border-slate-100 px-3 py-3">
        <div class="rounded-xl bg-emerald-50 px-2 py-2 text-center ring-1 ring-emerald-100">
            <p class="text-lg font-bold text-emerald-600">{{ $charts['total'] }}</p>
            <p class="text-[9px] font-medium text-slate-500">Total</p>
        </div>
        <div class="rounded-xl bg-orange-50 px-2 py-2 text-center ring-1 ring-orange-100">
            <p class="text-lg font-bold text-orange-600">{{ $charts['highRiskCount'] }}</p>
            <p class="text-[9px] font-medium text-slate-500">Risiko tinggi</p>
        </div>
        <div class="rounded-xl bg-rose-50 px-2 py-2 text-center ring-1 ring-rose-100">
            <p class="text-lg font-bold text-rose-600">{{ $charts['emergencyCount'] }}</p>
            <p class="text-[9px] font-medium text-slate-500">Darurat</p>
        </div>
    </div>

    <div class="admin-dashboard-section__body">
        <x-admin.line-chart
            title="Skrining per hari"
            :data="$charts['overTime']"
            color="#059669"
            meta="14 hari"
        />
        <x-admin.donut-chart title="Tingkat risiko" :items="$charts['riskDonutItems']" />
        <x-admin.bar-chart title="Skrining per penyakit" :items="$charts['diseaseBarItems']" />
        <x-admin.donut-chart
            title="Pengguna vs tamu"
            center-label="orang"
            :items="[
                ['label' => 'Terdaftar', 'value' => $charts['guestVsRegistered']['registered'], 'color' => '#0066ff'],
                ['label' => 'Tamu', 'value' => $charts['guestVsRegistered']['guest'], 'color' => '#94a3b8'],
            ]"
        />
    </div>
</section>
