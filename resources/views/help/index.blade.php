@extends('layouts.mobile')

@section('content')
    <x-mobile.page-header title="Bantuan" />

    <p class="mb-6 text-sm text-slate-500">Panduan fitur ChatSimpel Keperawatan Pintar.</p>

    @foreach ([
        ['Fitur', [
            ['label' => 'Skrining & Chatbot AI', 'route' => 'detection.start', 'desc' => 'Deteksi kesehatan interaktif'],
            ['label' => 'Edukasi Kesehatan', 'route' => 'education.index', 'desc' => 'Artikel kesehatan'],
            ['label' => 'Peringatan Darurat', 'route' => 'emergency', 'desc' => 'Hotline & gejala darurat'],
        ]],
        ['Akun (perlu login)', [
            ['label' => 'Dashboard', 'route' => 'dashboard', 'desc' => 'Ringkasan kesehatan Anda', 'auth' => true],
            ['label' => 'Riwayat Skrining', 'route' => 'history', 'desc' => 'Hasil skrining tersimpan', 'auth' => true],
            ['label' => 'Self Management', 'route' => 'self-management', 'desc' => 'Kelola aktivitas harian', 'auth' => true],
            ['label' => 'Monitoring', 'route' => 'monitoring', 'desc' => 'Catat tekanan darah & vital', 'auth' => true],
        ]],
    ] as $section)
        <h2 class="mb-3 text-sm font-bold text-slate-900">{{ $section[0] }}</h2>
        @foreach ($section[1] as $item)
            @if (empty($item['auth']) || auth()->check())
                <a href="{{ route($item['route']) }}" class="mb-2 block rounded-2xl bg-white px-4 py-3 shadow-card border border-brand-100">
                    <p class="font-semibold text-slate-900">{{ $item['label'] }}</p>
                    <p class="text-xs text-slate-500">{{ $item['desc'] }}</p>
                </a>
            @endif
        @endforeach
    @endforeach

    @guest
        <p class="mt-4 text-center text-xs text-slate-400">
            <a href="{{ route('login') }}" class="font-semibold text-brand-600">Masuk</a> untuk mengakses fitur yang memerlukan akun.
        </p>
    @endguest
@endsection
