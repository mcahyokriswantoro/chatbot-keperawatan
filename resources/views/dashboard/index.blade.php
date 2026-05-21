@extends('layouts.mobile')

@section('content')
    <x-mobile.page-header title="Dashboard" />

    <p class="mb-6 text-sm text-slate-500">
        Halo, <span class="font-semibold text-slate-800">{{ auth()->user()->name }}</span>
    </p>

    <div class="mb-6 grid grid-cols-2 gap-3">
        <div class="rounded-2xl bg-white p-4 shadow-card border border-brand-100">
            <p class="text-xs text-slate-500">Skrining</p>
            <p class="mt-1 text-2xl font-bold text-brand-600">{{ $screeningCount }}</p>
        </div>
        <div class="rounded-2xl bg-white p-4 shadow-card border border-brand-100">
            <p class="text-xs text-slate-500">Monitoring</p>
            <p class="mt-1 text-2xl font-bold text-brand-600">{{ $monitoringCount }}</p>
        </div>
        <div class="rounded-2xl bg-white p-4 shadow-card border border-brand-100 col-span-2">
            <p class="text-xs text-slate-500">Tugas Self Management</p>
            <p class="mt-1 text-2xl font-bold text-brand-600">{{ $pendingTasks }} belum selesai</p>
        </div>
    </div>

    <section class="mb-6 space-y-3">
        <h2 class="text-sm font-bold text-slate-900">Akses Cepat</h2>
        @foreach ([
            ['label' => 'Mulai Skrining', 'route' => 'detection.start', 'desc' => 'Chatbot deteksi kesehatan'],
            ['label' => 'Riwayat Skrining', 'route' => 'history', 'desc' => 'Lihat hasil sebelumnya'],
            ['label' => 'Self Management', 'route' => 'self-management', 'desc' => 'Kelola aktivitas harian'],
            ['label' => 'Monitoring', 'route' => 'monitoring', 'desc' => 'Catat tekanan darah & vital'],
            ['label' => 'Edukasi Kesehatan', 'route' => 'education.index', 'desc' => 'Artikel kesehatan'],
            ['label' => 'Peringatan Darurat', 'route' => 'emergency', 'desc' => 'Hotline & gejala darurat'],
        ] as $item)
            <a href="{{ route($item['route']) }}" class="block rounded-2xl bg-white px-4 py-3 shadow-card border border-brand-100 hover:border-brand-300">
                <p class="font-semibold text-slate-900">{{ $item['label'] }}</p>
                <p class="text-xs text-slate-500">{{ $item['desc'] }}</p>
            </a>
        @endforeach
    </section>

    @if ($latestScreening)
        <section class="rounded-2xl bg-brand-50 border border-brand-100 p-4">
            <h2 class="text-sm font-bold text-brand-800">Skrining Terakhir</h2>
            <p class="mt-2 text-xs text-slate-600">{{ $latestScreening->created_at->format('d M Y, H:i') }}</p>
            <p class="mt-1 text-sm font-medium capitalize text-slate-700">Risiko: {{ $latestScreening->risk_level }}</p>
            <a href="{{ route('history.show', $latestScreening->id) }}" class="mt-3 inline-block text-sm font-semibold text-brand-600">Lihat detail →</a>
        </section>
    @endif
@endsection
