@extends('layouts.admin')

@section('title', 'Dashboard')

@push('styles')
    @php($monitoringCssVer = filemtime(public_path('css/monitoring-choices.css')) ?: time())
    <link rel="stylesheet" href="/css/monitoring-choices.css?v={{ $monitoringCssVer }}">
@endpush

@section('content')
<div class="space-y-5">
    <x-admin.hero />

    {{-- Statistik overlap --}}
    <div class="-mt-12 rounded-2xl bg-white p-4 shadow-lg ring-1 ring-slate-100">
        <div class="grid grid-cols-2 gap-3">
            <div class="rounded-xl bg-brand-50/80 px-3 py-2.5 text-center ring-1 ring-brand-100">
                <p class="text-2xl font-bold text-brand-600">{{ $userCount }}</p>
                <p class="mt-0.5 text-[10px] font-medium text-slate-500">Pengguna</p>
                <p class="text-[9px] text-emerald-600">+{{ $newUsersWeek }} minggu ini</p>
            </div>
            <div class="rounded-xl bg-emerald-50/80 px-3 py-2.5 text-center ring-1 ring-emerald-100">
                <p class="text-2xl font-bold text-emerald-600">{{ $screeningCount }}</p>
                <p class="mt-0.5 text-[10px] font-medium text-slate-500">Skrining</p>
                <p class="text-[9px] text-slate-400">{{ $identityCount }} identitas</p>
            </div>
            <div class="rounded-xl bg-violet-50/80 px-3 py-2.5 text-center ring-1 ring-violet-100">
                <p class="text-2xl font-bold text-violet-600">{{ $monitoringCount }}</p>
                <p class="mt-0.5 text-[10px] font-medium text-slate-500">Monitoring</p>
            </div>
            <div class="rounded-xl bg-rose-50/80 px-3 py-2.5 text-center ring-1 ring-rose-100">
                <p class="text-2xl font-bold text-rose-600">{{ $highRiskCount }}</p>
                <p class="mt-0.5 text-[10px] font-medium text-slate-500">Risiko tinggi</p>
                <p class="text-[9px] text-rose-500">{{ $emergencyCount }} darurat</p>
            </div>
            <div class="rounded-xl bg-sky-50/80 px-3 py-2.5 text-center ring-1 ring-sky-100">
                <p class="text-2xl font-bold text-sky-600">{{ $consultationPendingCount }}</p>
                <p class="mt-0.5 text-[10px] font-medium text-slate-500">Bayar pending</p>
                <p class="text-[9px] text-slate-400">{{ $consultationPaidCount }} disetujui</p>
            </div>
        </div>
    </div>

    @if ($highRiskCount > 0)
        <a href="{{ route('admin.screenings.index', ['risk' => 'high']) }}" class="flex items-center gap-3 rounded-2xl border-l-4 border-rose-400 bg-rose-50 px-4 py-3 transition active:scale-[0.99]">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-rose-100 text-lg">⚠️</span>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-bold text-rose-900">{{ $highRiskCount }} skrining risiko tinggi</p>
                <p class="text-[11px] text-rose-700">Perlu ditinjau — ketuk untuk lihat daftar</p>
            </div>
            <svg class="h-4 w-4 shrink-0 text-rose-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
        </a>
    @endif

    @if ($consultationPendingCount > 0)
        <a href="{{ route('admin.consultations.index', ['status' => 'pending']) }}" class="flex items-center gap-3 rounded-2xl border-l-4 border-amber-400 bg-amber-50 px-4 py-3 transition active:scale-[0.99]">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-lg">💬</span>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-bold text-amber-900">{{ $consultationPendingCount }} pembayaran konsultasi menunggu</p>
                <p class="text-[11px] text-amber-800">Verifikasi transfer Giro BRI — ketuk untuk setujui/tolak</p>
            </div>
            <svg class="h-4 w-4 shrink-0 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
        </a>
    @endif

    @if ($medicinePendingCount > 0)
        <a href="{{ route('admin.medicines.index', ['status' => 'pending']) }}" class="flex items-center gap-3 rounded-2xl border-l-4 border-blue-400 bg-blue-50/50 px-4 py-3 transition active:scale-[0.99]">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-lg">💊</span>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-bold text-blue-900">{{ $medicinePendingCount }} pembayaran obat menunggu</p>
                <p class="text-[11px] text-blue-800">Verifikasi transfer Giro BRI — ketuk untuk setujui/tolak</p>
            </div>
            <svg class="h-4 w-4 shrink-0 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
        </a>
    @endif

    @if ($homecarePendingCount > 0)
        <a href="{{ route('admin.homecare.index', ['status' => 'pending']) }}" class="flex items-center gap-3 rounded-2xl border-l-4 border-indigo-400 bg-indigo-50/50 px-4 py-3 transition active:scale-[0.99]">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-lg">🏠</span>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-bold text-indigo-900">{{ $homecarePendingCount }} booking homecare menunggu</p>
                <p class="text-[11px] text-indigo-800">Verifikasi transfer Giro BRI — ketuk untuk setujui/tolak</p>
            </div>
            <svg class="h-4 w-4 shrink-0 text-indigo-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
        </a>
    @endif

    {{-- Dashboard Hasil Skrining --}}
    <section class="admin-dashboard-section">
        <div class="admin-dashboard-section__head">
            <h2 class="admin-dashboard-section__title">Dashboard Hasil Skrining</h2>
            <a href="{{ route('admin.screenings.index') }}" class="admin-dashboard-section__link">Lihat semua →</a>
        </div>
        <div class="admin-dashboard-section__body">
            <x-admin.line-chart
                title="Skrining per hari"
                :data="$screeningsOverTime"
                color="#059669"
                meta="14 hari"
            />
            <x-admin.donut-chart title="Tingkat risiko" :items="$riskChartItems" />
            <x-admin.bar-chart title="Skrining per penyakit" :items="$diseaseBarItems" />
            <x-admin.donut-chart
                title="Pengguna vs tamu"
                center-label="orang"
                :items="[
                    ['label' => 'Terdaftar', 'value' => $screeningsGuestVsRegistered['registered'], 'color' => '#0066ff'],
                    ['label' => 'Tamu', 'value' => $screeningsGuestVsRegistered['guest'], 'color' => '#94a3b8'],
                ]"
            />
        </div>
    </section>

    {{-- Dashboard Monitoring --}}
    <section class="admin-dashboard-section">
        <div class="admin-dashboard-section__head">
            <h2 class="admin-dashboard-section__title">Dashboard Monitoring</h2>
            <div class="flex items-center gap-2">
                @if ($showMonthlyDetail && ($monthlyOverview['period_label'] ?? null))
                    <span class="text-[10px] font-semibold text-brand-600">{{ $monthlyOverview['period_label'] }}</span>
                @endif
                <a href="{{ route('admin.monitoring.index', $showMonthlyDetail ? array_filter(['period_from' => $periodFrom ?? '', 'period_to' => $periodTo ?? '']) : []) }}" class="admin-dashboard-section__link">Lihat semua →</a>
            </div>
        </div>
        <div class="admin-dashboard-section__body">
            @include('admin.partials.monitoring-monthly-section')

            @if ($showMonthlyDetail)
                <x-admin.line-chart
                    title="Entri monitoring per hari"
                    :data="$monitoringOverTime"
                    color="#7c3aed"
                    :meta="$monitoringPeriodLabel ?? ''"
                />
                <x-admin.donut-chart title="Jenis monitoring" :items="$monitoringTypeDonutItems" />
                <x-admin.bar-chart title="Monitoring per penyakit" :items="$monitoringDiseaseItems" />
                <x-admin.bar-chart
                    title="Rata-rata skor keluhan"
                    :items="$monitoringAverageBars['complaint']"
                    :max="100"
                    hint="Parameter: ≤25% Baik · ≤50% Cukup · >50% Kurang"
                />
                <x-admin.bar-chart
                    title="Rata-rata self management"
                    :items="$monitoringAverageBars['selfManagement']"
                    :max="100"
                    hint="Parameter: ≥80% Baik · ≥60% Cukup · <60% Kurang"
                />
                <x-admin.bar-chart
                    title="Rata-rata kepatuhan obat"
                    :items="$monitoringAverageBars['compliance']"
                    :max="100"
                    hint="Parameter: ≥80% Baik · ≥60% Cukup · <60% Kurang · Dihitung dari hari tepat waktu ÷ durasi resep"
                />
            @endif
        </div>
    </section>

    {{-- Menu cepat --}}
    <section>
        <h2 class="mb-3 text-sm font-bold text-slate-900">Kelola data</h2>
        <div class="grid grid-cols-2 gap-3">
            <x-admin.action-tile
                :url="route('admin.users.index')"
                label="Pengguna"
                sub="Daftar & profil"
                bg="from-brand-600 to-brand-500"
                icon="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"
            />
            <x-admin.action-tile
                :url="route('admin.screenings.index')"
                label="Skrining"
                sub="Hasil & risiko"
                bg="from-emerald-600 to-teal-500"
                icon="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"
            />
            <x-admin.action-tile
                :url="route('admin.monitoring.index')"
                label="Monitoring"
                sub="Tanda vital"
                bg="from-violet-600 to-purple-500"
                icon="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"
            />
            <x-admin.action-tile
                :url="route('admin.articles.index')"
                label="Video"
                sub="Edukasi kesehatan"
                bg="from-slate-700 to-slate-600"
                icon="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"
            />
            <x-admin.action-tile
                :url="route('admin.consultations.providers.index')"
                label="Tenaga kesehatan"
                sub="Perawat & dokter"
                bg="from-sky-600 to-cyan-500"
                icon="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"
            />
            <x-admin.action-tile
                :url="route('admin.consultations.index')"
                label="Konsultasi"
                :sub="$consultationPendingCount > 0 ? $consultationPendingCount.' pending verifikasi' : 'Verifikasi BRI'"
                bg="from-teal-600 to-emerald-500"
                icon="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z"
            />
            <x-admin.action-tile
                :url="route('admin.medicines.index')"
                label="Obat & Vitamin"
                :sub="$medicinePendingCount > 0 ? $medicinePendingCount.' pending verifikasi' : 'Katalog & Pesanan'"
                bg="from-sky-700 to-sky-600"
                icon="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"
            />
            <x-admin.action-tile
                :url="route('admin.homecare.index')"
                label="Homecare"
                :sub="$homecarePendingCount > 0 ? $homecarePendingCount.' pending verifikasi' : 'Paket & Jadwal'"
                bg="from-emerald-700 to-teal-600"
                icon="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"
            />
        </div>
    </section>

    @if ($consultationPendingCount > 0)
        <section>
            <div class="mb-3 flex items-center justify-between">
                <h2 class="text-sm font-bold text-slate-900">Verifikasi pembayaran konsultasi</h2>
                <a href="{{ route('admin.consultations.index', ['status' => 'pending']) }}" class="text-[11px] font-semibold text-brand-600">Semua →</a>
            </div>
            <div class="space-y-3">
                @foreach ($pendingConsultationOrders as $order)
                    @include('admin.partials.consultation-order-card', ['order' => $order])
                @endforeach
            </div>
        </section>
    @endif

    <a href="{{ route('admin.access.index') }}" class="flex items-center gap-3 rounded-2xl border border-amber-100 bg-gradient-to-r from-amber-50 to-orange-50 p-4 shadow-sm ring-1 ring-amber-100/80 transition active:scale-[0.99]">
        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-white text-xl shadow-sm ring-1 ring-amber-100">🔐</span>
        <div class="min-w-0 flex-1">
            <p class="text-sm font-bold text-slate-900">Kelola akses admin</p>
            <p class="text-[11px] text-slate-500">Tambah admin baru via email terdaftar</p>
        </div>
        <svg class="h-4 w-4 shrink-0 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
    </a>

    <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-3 rounded-2xl border border-brand-100 bg-gradient-to-r from-brand-50 to-sky-50 p-4 shadow-sm ring-1 ring-brand-100/80 transition active:scale-[0.99] mt-3">
        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-white text-xl shadow-sm ring-1 ring-brand-100">📱</span>
        <div class="min-w-0 flex-1">
            <p class="text-sm font-bold text-slate-900">Pengaturan Notifikasi WA</p>
            <p class="text-[11px] text-slate-500">Ubah nomor WA Admin, Apotek & Homecare</p>
        </div>
        <svg class="h-4 w-4 shrink-0 text-brand-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
    </a>


    <section>
        <div class="mb-3 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900">Skrining terbaru</h2>
            <a href="{{ route('admin.screenings.index') }}" class="text-[11px] font-semibold text-brand-600">Semua →</a>
        </div>
        <div class="space-y-2">
            @forelse ($recentScreenings as $s)
                @include('admin.partials.screening-list-button', [
                    'session' => $s,
                    'detailUrl' => route('admin.screenings.show', $s),
                ])
            @empty
                <div class="rounded-2xl border border-dashed border-brand-200 bg-brand-50/30 p-8 text-center">
                    <img src="{{ asset('images/robot.png') }}" alt="" class="mx-auto h-16 w-16 object-contain opacity-80">
                    <p class="mt-3 text-sm text-slate-500">Belum ada skrining tercatat.</p>
                </div>
            @endforelse
        </div>
    </section>

    <section>
        <div class="mb-3 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900">Monitoring terbaru</h2>
            <a href="{{ route('admin.monitoring.index') }}" class="text-[11px] font-semibold text-brand-600">Semua →</a>
        </div>
        <div class="space-y-2">
            @forelse ($recentMonitoring as $entry)
                @include('admin.partials.monitoring-list-button', ['entry' => $entry])
            @empty
                <div class="rounded-2xl border border-dashed border-violet-200 bg-violet-50/30 p-8 text-center">
                    <p class="text-sm text-slate-500">Belum ada monitoring tercatat.</p>
                </div>
            @endforelse
        </div>
    </section>

    <section>
        <div class="mb-3 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900">Pengguna baru</h2>
            <a href="{{ route('admin.users.index') }}" class="text-[11px] font-semibold text-brand-600">Semua →</a>
        </div>
        <div class="space-y-3">
            @forelse ($recentUsers as $user)
                @include('admin.partials.user-list-card', ['user' => $user])
            @empty
                <div class="rounded-2xl border border-dashed border-slate-200 bg-white p-6 text-center">
                    <p class="text-sm text-slate-500">Belum ada pendaftar.</p>
                </div>
            @endforelse
        </div>
    </section>

    <section>
        <div class="mb-3 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900">Konsultasi terbaru</h2>
            <a href="{{ route('admin.consultations.index') }}" class="text-[11px] font-semibold text-brand-600">Semua →</a>
        </div>
        <div class="space-y-2">
            @forelse ($recentConsultationOrders as $order)
                @include('admin.partials.consultation-list-button', ['order' => $order])
            @empty
                <div class="rounded-2xl border border-dashed border-sky-200 bg-sky-50/30 p-8 text-center">
                    <p class="text-sm text-slate-500">Belum ada pembayaran konsultasi.</p>
                </div>
            @endforelse
        </div>
    </section>

    <section>
        <div class="mb-3 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900">Ringkasan aktivitas</h2>
            <a href="{{ route('admin.screenings.index') }}" class="text-[11px] font-semibold text-brand-600">Semua →</a>
        </div>
        <div class="space-y-2">
            @forelse ($patientActivitySummaries as $summary)
                @include('admin.partials.activity-summary', ['summary' => $summary])
            @empty
                <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50/30 p-6 text-center">
                    <p class="text-sm text-slate-500">Belum ada aktivitas tercatat di sistem.</p>
                </div>
            @endforelse
        </div>
    </section>
</div>
@endsection
