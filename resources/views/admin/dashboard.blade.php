@extends('layouts.admin')

@section('title', 'Dashboard')

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
    @else
        <div class="flex gap-3 rounded-2xl border border-brand-100 bg-brand-50/60 px-4 py-3">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-brand-100">
                <img src="{{ asset('images/robot.png') }}?v={{ filemtime(public_path('images/robot.png')) ?: time() }}" alt="" class="h-8 w-8 object-contain">
            </span>
            <p class="text-xs leading-relaxed text-slate-600">
                <span class="font-semibold text-slate-800">Semua terpantau.</span>
                Data pengguna dan hasil skrining tersimpan aman di panel admin.
            </p>
        </div>
    @endif

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
                label="Artikel"
                sub="Edukasi kesehatan"
                bg="from-slate-700 to-slate-600"
                icon="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"
            />
        </div>
    </section>

    <a href="{{ route('admin.access.index') }}" class="flex items-center gap-3 rounded-2xl border border-amber-100 bg-gradient-to-r from-amber-50 to-orange-50 p-4 shadow-sm ring-1 ring-amber-100/80 transition active:scale-[0.99]">
        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-white text-xl shadow-sm ring-1 ring-amber-100">🔐</span>
        <div class="min-w-0 flex-1">
            <p class="text-sm font-bold text-slate-900">Kelola akses admin</p>
            <p class="text-[11px] text-slate-500">Tambah admin baru via email terdaftar</p>
        </div>
        <svg class="h-4 w-4 shrink-0 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
    </a>

    {{-- Ringkasan grafis --}}
    <section class="overflow-hidden rounded-2xl bg-gradient-to-r from-brand-50 to-blue-50 ring-1 ring-brand-100">
        <div class="flex items-center gap-3 border-b border-brand-100/80 px-4 py-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white shadow-sm ring-1 ring-brand-100">
                <x-app.medical-note-icon class="h-7 w-7" />
            </div>
            <div>
                <h2 class="text-sm font-bold text-slate-900">Skrining per penyakit</h2>
                <a href="{{ route('admin.screenings.index') }}" class="text-[11px] font-semibold text-brand-600">Lihat semua →</a>
            </div>
        </div>
        <div class="divide-y divide-brand-100/60">
            @forelse ($screeningsByDisease as $disease => $total)
                <div class="flex items-center justify-between px-4 py-3 text-sm">
                    <span class="text-slate-700">{{ $stats->diseaseLabel($disease) }}</span>
                    <span class="rounded-full bg-white px-2.5 py-0.5 text-xs font-bold text-brand-700 ring-1 ring-brand-100">{{ $total }}</span>
                </div>
            @empty
                <p class="px-4 py-6 text-center text-xs text-slate-500">Belum ada data skrining.</p>
            @endforelse
        </div>
    </section>

    <section class="rounded-2xl border border-brand-100 bg-white shadow-sm overflow-hidden">
        <div class="border-b border-slate-100 bg-slate-50/80 px-4 py-3">
            <h2 class="text-sm font-bold text-slate-900">Tingkat risiko</h2>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse ($screeningsByRisk as $level => $total)
                @php
                    $chip = match ($level) {
                        'low' => 'bg-emerald-50 text-emerald-700 ring-emerald-100',
                        'medium' => 'bg-amber-50 text-amber-800 ring-amber-100',
                        'high' => 'bg-orange-50 text-orange-800 ring-orange-100',
                        'emergency' => 'bg-rose-50 text-rose-700 ring-rose-100',
                        default => 'bg-slate-50 text-slate-700 ring-slate-100',
                    };
                @endphp
                <div class="flex items-center justify-between px-4 py-3 text-sm">
                    <span class="text-slate-700">{{ $stats->riskLabel($level) }}</span>
                    <span class="rounded-full px-2.5 py-0.5 text-xs font-bold ring-1 {{ $chip }}">{{ $total }}</span>
                </div>
            @empty
                <p class="px-4 py-6 text-center text-xs text-slate-500">Belum ada data.</p>
            @endforelse
        </div>
    </section>

    <section>
        <div class="mb-3 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900">Skrining terbaru</h2>
            <a href="{{ route('admin.screenings.index') }}" class="text-[11px] font-semibold text-brand-600">Semua →</a>
        </div>
        <div class="space-y-3">
            @forelse ($recentScreenings as $s)
                @include('admin.partials.screening-list-card', [
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
</div>
@endsection
