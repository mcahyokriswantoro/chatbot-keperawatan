@extends('layouts.mobile')

@php
    $user = auth()->user();
    $firstName = explode(' ', trim($user->name))[0];
    $hour = (int) now()->format('H');
    $greeting = $hour < 11 ? 'Selamat pagi' : ($hour < 15 ? 'Selamat siang' : ($hour < 18 ? 'Selamat sore' : 'Selamat malam'));

    $bmi = null;
    $bmiLabel = null;
    if ($user->weight && $user->height && (float) $user->height > 0) {
        $heightM = (float) $user->height / 100;
        $bmi = round((float) $user->weight / ($heightM * $heightM), 1);
        $bmiLabel = match (true) {
            $bmi < 18.5 => 'Kurus',
            $bmi < 25 => 'Normal',
            $bmi < 30 => 'Berlebih',
            default => 'Obesitas',
        };
    }

    $actions = [
        ['url' => route('detection.start'), 'label' => 'Skrining', 'sub' => 'Deteksi via chatbot', 'bg' => 'from-brand-600 to-brand-500', 'icon' => 'M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z'],
        ['url' => route('monitoring'), 'label' => 'Monitoring', 'sub' => 'Catat tanda vital', 'bg' => 'from-emerald-600 to-teal-500', 'icon' => 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z'],
        ['url' => route('self-management'), 'label' => 'Self Management', 'sub' => 'Panduan perawatan', 'bg' => 'from-violet-600 to-purple-500', 'icon' => 'M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25'],
        ['url' => route('history'), 'label' => 'Riwayat', 'sub' => 'Hasil skrining', 'bg' => 'from-slate-700 to-slate-600', 'icon' => 'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z'],
    ];

    $insight = match (true) {
        $pendingTasks > 0 => "Anda punya {$pendingTasks} tugas self management yang belum selesai — lanjutkan agar perawatan tetap konsisten.",
        $screeningCount === 0 => 'Belum ada skrining tercatat. Mulai percakapan dengan chatbot untuk deteksi dini kondisi kesehatan.',
        $monitoringCount === 0 => 'Catat monitoring rutin supaya perkembangan kesehatan Anda lebih mudah dipantau.',
        default => 'Terus jaga kesehatan dengan skrining berkala dan catatan monitoring yang rutin.',
    };
@endphp

@section('content')
<div class="space-y-5">
    {{-- Header gelap — beda dari beranda --}}
    <header class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-800 via-slate-900 to-brand-900 px-5 pb-16 pt-5 text-white shadow-lg">
        <div class="pointer-events-none absolute -right-10 top-0 h-40 w-40 rounded-full bg-brand-500/20 blur-2xl"></div>
        <div class="pointer-events-none absolute -left-6 bottom-0 h-24 w-24 rounded-full bg-teal-400/10 blur-xl"></div>

        <div class="relative flex items-start justify-between gap-3">
            <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-slate-400">{{ now()->translatedFormat('l, d F Y') }}</p>
                <h1 class="mt-1 text-xl font-bold leading-snug">{{ $greeting }}, {{ $firstName }}</h1>
                <p class="mt-1 text-xs text-slate-400">Ringkasan kesehatan & aktivitas Anda</p>
            </div>
            <a
                href="{{ route('profile.page') }}"
                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-white/10 text-base font-bold ring-1 ring-white/20 backdrop-blur-sm transition hover:bg-white/20"
                title="Profil"
            >
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </a>
        </div>
    </header>

    {{-- Kartu statistik — overlap header --}}
    <div class="-mt-12 rounded-2xl bg-white p-4 shadow-lg ring-1 ring-slate-100">
        <div class="grid grid-cols-3 divide-x divide-slate-100">
            <div class="px-2 text-center">
                <p class="text-2xl font-bold text-brand-600">{{ $screeningCount }}</p>
                <p class="mt-0.5 text-[10px] font-medium text-slate-500">Skrining</p>
            </div>
            <div class="px-2 text-center">
                <p class="text-2xl font-bold text-emerald-600">{{ $monitoringCount }}</p>
                <p class="mt-0.5 text-[10px] font-medium text-slate-500">Monitoring</p>
            </div>
            <div class="px-2 text-center">
                <p @class(['text-2xl font-bold', $pendingTasks > 0 ? 'text-amber-500' : 'text-slate-400'])>{{ $pendingTasks }}</p>
                <p class="mt-0.5 text-[10px] font-medium text-slate-500">Tugas aktif</p>
            </div>
        </div>
        @if ($bmi)
            <div class="mt-3 flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2">
                <span class="text-[11px] text-slate-500">Indeks Massa Tubuh</span>
                <span class="text-xs font-bold text-slate-800">{{ $bmi }} <span class="font-medium text-slate-500">· {{ $bmiLabel }}</span></span>
            </div>
        @endif
    </div>

    {{-- Insight singkat --}}
    <div class="flex gap-3 rounded-2xl border border-brand-100 bg-brand-50/60 px-4 py-3">
        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-brand-600 text-sm text-white" aria-hidden="true">💬</span>
        <p class="text-xs leading-relaxed text-slate-600">{{ $insight }}</p>
    </div>

    @if ($pendingTasks > 0)
        <a
            href="{{ route('self-management') }}"
            class="flex items-center gap-3 rounded-2xl border-l-4 border-amber-400 bg-amber-50 px-4 py-3 transition active:scale-[0.99]"
        >
            <div class="min-w-0 flex-1">
                <p class="text-sm font-bold text-amber-900">{{ $pendingTasks }} tugas menunggu</p>
                <p class="text-[11px] text-amber-700">Selesaikan panduan self management hari ini</p>
            </div>
            <svg class="h-4 w-4 shrink-0 text-amber-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
        </a>
    @endif

    {{-- Aksi utama — grid 2x2, bukan ikon beranda --}}
    <section>
        <h2 class="mb-3 text-sm font-bold text-slate-900">Apa yang ingin Anda lakukan?</h2>
        <div class="grid grid-cols-2 gap-3">
            @foreach ($actions as $action)
                <a
                    href="{{ $action['url'] }}"
                    class="group relative overflow-hidden rounded-2xl bg-gradient-to-br {{ $action['bg'] }} p-4 text-white shadow-md transition active:scale-[0.97] hover:shadow-lg"
                >
                    <div class="pointer-events-none absolute -right-3 -top-3 h-16 w-16 rounded-full bg-white/10"></div>
                    <svg class="relative h-6 w-6 opacity-90" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $action['icon'] }}"/>
                    </svg>
                    <p class="relative mt-3 text-sm font-bold">{{ $action['label'] }}</p>
                    <p class="relative text-[10px] text-white/75">{{ $action['sub'] }}</p>
                </a>
            @endforeach
        </div>
    </section>

    {{-- Feed aktivitas --}}
    <section>
        <div class="mb-3 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900">Aktivitas Terbaru</h2>
            @if ($latestScreening || $latestMonitoring)
                <a href="{{ route('history') }}" class="text-[11px] font-semibold text-brand-600">Semua →</a>
            @endif
        </div>

        @if ($latestScreening || $latestMonitoring)
            <div class="relative space-y-0">
                @if ($latestScreening)
                    @php
                        $riskLevel = $latestScreening->displayRiskLevel();
                        $riskDot = match ($riskLevel) {
                            'low' => 'bg-emerald-500',
                            'medium' => 'bg-amber-500',
                            'high', 'emergency' => 'bg-rose-500',
                            default => 'bg-slate-400',
                        };
                    @endphp
                    <a
                        href="{{ route('history.show', $latestScreening->id) }}"
                        class="group relative flex gap-3 border-l-2 border-slate-200 py-3 pl-4 transition hover:border-brand-400"
                    >
                        <span @class(['absolute -left-[5px] top-4 h-2 w-2 rounded-full ring-2 ring-white', $riskDot])></span>
                        <div class="min-w-0 flex-1">
                            <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Skrining</p>
                            <p class="font-semibold text-slate-900">{{ $latestScreening->diseaseLabel() ?? 'Deteksi Kesehatan' }}</p>
                            <p class="mt-0.5 text-[11px] text-slate-500">
                                {{ $latestScreening->formattedDateTime() }}
                                · Risiko {{ $latestScreening->displayRiskLabel() }}
                            </p>
                        </div>
                        <svg class="mt-1 h-4 w-4 shrink-0 text-slate-300 group-hover:text-brand-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                    </a>
                @endif

                @if ($latestMonitoring)
                    <a
                        href="{{ route('monitoring') }}"
                        class="group relative flex gap-3 border-l-2 border-slate-200 py-3 pl-4 transition hover:border-emerald-400"
                    >
                        <span class="absolute -left-[5px] top-4 h-2 w-2 rounded-full bg-emerald-500 ring-2 ring-white"></span>
                        <div class="min-w-0 flex-1">
                            <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Monitoring</p>
                            <p class="font-semibold text-slate-900">
                                {{ $latestMonitoring->recorded_at?->translatedFormat('d M Y') ?? $latestMonitoring->created_at->translatedFormat('d M Y') }}
                            </p>
                            @if ($latestMonitoring->bloodPressureLabel() || $latestMonitoring->heart_rate)
                                <p class="mt-0.5 text-[11px] text-slate-500">
                                    @if ($latestMonitoring->bloodPressureLabel())
                                        TD {{ $latestMonitoring->bloodPressureLabel() }}
                                    @endif
                                    @if ($latestMonitoring->heart_rate)
                                        {{ $latestMonitoring->bloodPressureLabel() ? '·' : '' }} Nadi {{ $latestMonitoring->heart_rate }} bpm
                                    @endif
                                </p>
                            @endif
                        </div>
                        <svg class="mt-1 h-4 w-4 shrink-0 text-slate-300 group-hover:text-emerald-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                    </a>
                @endif
            </div>
        @else
            <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50/80 px-4 py-8 text-center">
                <p class="text-sm font-medium text-slate-600">Belum ada aktivitas</p>
                <p class="mt-1 text-xs text-slate-400">Mulai skrining atau catat monitoring pertama Anda</p>
                <a href="{{ route('detection.start') }}" class="mt-4 inline-block rounded-full bg-brand-600 px-5 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-brand-700">
                    Mulai Skrining
                </a>
            </div>
        @endif
    </section>

    {{-- Tautan sekunder — baris pill, bukan banner edukasi --}}
    <section class="flex flex-wrap gap-2 pb-2">
        <a href="{{ route('education.index') }}" class="rounded-full border border-slate-200 bg-white px-3.5 py-2 text-[11px] font-semibold text-slate-600 shadow-sm transition hover:border-brand-200 hover:text-brand-600">
            Edukasi
        </a>
        <a href="{{ route('help') }}" class="rounded-full border border-slate-200 bg-white px-3.5 py-2 text-[11px] font-semibold text-slate-600 shadow-sm transition hover:border-brand-200 hover:text-brand-600">
            Bantuan Chatbot
        </a>
        <a href="{{ route('emergency') }}" class="rounded-full border border-rose-200 bg-rose-50 px-3.5 py-2 text-[11px] font-semibold text-rose-600 shadow-sm transition hover:bg-rose-100">
            Darurat
        </a>
        <a href="{{ route('profile.edit') }}" class="rounded-full border border-slate-200 bg-white px-3.5 py-2 text-[11px] font-semibold text-slate-600 shadow-sm transition hover:border-brand-200 hover:text-brand-600">
            Edit Profil
        </a>
    </section>
</div>
@endsection
