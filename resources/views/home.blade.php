@extends('layouts.mobile')

@php
    $detectionUrl = auth()->check()
        ? route('detection.identity')
        : route('login');
    $historyUrl = auth()->check() ? route('history') : route('login');

    $features = [
        [
            'label' => 'Deteksi Kesehatan',
            'desc' => 'Cek kondisi kesehatan Anda',
            'url' => $detectionUrl,
            'icon' => 'images/unggulan_deteksi.png',
        ],
        [
            'label' => 'Riwayat Kesehatan',
            'desc' => 'Lihat riwayat deteksi Anda',
            'url' => $historyUrl,
            'icon' => 'images/unggulan_riwayat.png',
        ],
        [
            'label' => 'Layanan Apotek',
            'desc' => 'Beli obat & vitamin online',
            'url' => route('medicines.index'),
            'icon' => 'images/unggulan_obat.png',
        ],
        [
            'label' => 'Layanan Homecare',
            'desc' => 'Panggil perawat ke rumah',
            'url' => route('homecare.index'),
            'icon' => 'images/unggulan_homecare.png',
        ],
        [
            'label' => 'Edukasi Kesehatan',
            'desc' => 'Video edukasi kesehatan',
            'url' => route('education.index'),
            'icon' => 'images/unggulan_edukasi.png',
            'icon_class' => 'h-14 w-14',
            'img_class' => 'scale-120',
        ],
        [
            'label' => 'Konsultasi Langsung',
            'desc' => 'Tanya langsung ke ahli',
            'url' => route('consultation.index'),
            'icon' => 'images/unggulan_konsultasi.png',
        ],
    ];

@endphp

@section('content')
<div
    x-data="{
        tipIndex: 0,
        tips: @js($tips),
        init() {
            setInterval(() => {
                this.tipIndex = (this.tipIndex + 1) % this.tips.length;
            }, 5000);
        },
    }"
    class="space-y-6"
>
    {{-- Hero Banners 75:25 Split Grid on Laptop --}}
    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-stretch">
        {{-- Hero header (75% / 8 cols) --}}
        <header class="md:col-span-8 relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-50 via-white to-brand-100/80 p-5 shadow-soft ring-1 ring-brand-100/60 flex flex-col justify-between">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-start gap-3 min-w-0">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-full bg-white shadow-sm ring-2 ring-brand-100">
                        <x-app.medical-note-icon class="h-8 w-8" />
                    </div>
                    <div class="min-w-0 flex-1 pt-0.5">
                        <p class="text-xs font-semibold text-slate-500">Hi, Saya</p>
                        <h1 class="text-2xl font-black leading-tight tracking-tight sm:text-3xl">
                            <span class="text-[#002966]">Nersia</span> <span class="text-[#0aa4b0]">Health</span>
                        </h1>
                        <p class="text-[10px] font-bold text-slate-400 mt-0.5">Chatbot Smart Health Screening & Care</p>
                    </div>
                </div>
                <div class="relative h-20 w-20 shrink-0 animate-[float_3s_ease-in-out_infinite]">
                    <img
                        src="{{ asset('images/robot.png') }}?v={{ filemtime(public_path('images/robot.png')) }}"
                        alt="Robot Nersia"
                        class="h-full w-full object-contain drop-shadow-md"
                    />
                </div>
            </div>
            <p class="mt-3 text-xs leading-relaxed text-slate-600">
                Saya siap membantu deteksi dan merawat kesehatan Anda hari ini 💙
            </p>
        </header>

        {{-- Main CTA (25% / 4 cols) --}}
        <a
            href="{{ $detectionUrl }}"
            class="md:col-span-4 group relative flex flex-col justify-between overflow-hidden rounded-3xl bg-gradient-to-r from-brand-600 via-brand-500 to-brand-600 p-5 shadow-lg shadow-brand-600/25 transition active:scale-[0.98] hover:shadow-xl hover:shadow-brand-600/30 min-h-[140px]"
        >
            <div class="pointer-events-none absolute inset-0 opacity-20">
                <div class="absolute -right-8 -top-8 h-32 w-32 rounded-full bg-white"></div>
                <div class="absolute -bottom-6 left-1/3 h-24 w-24 rounded-full bg-white/60"></div>
            </div>
            <div class="relative flex flex-col justify-between h-full gap-3">
                <div>
                    <span class="inline-block rounded-full bg-white/20 px-2.5 py-0.5 text-[9px] font-bold text-white uppercase tracking-wider mb-1 backdrop-blur-xs">Skrining Mandiri</span>
                    <h2 class="text-base font-extrabold text-white leading-tight">Mulai Deteksi Kesehatan</h2>
                </div>
                <div class="flex items-center justify-between pt-2">
                    <p class="text-[11px] text-blue-100 font-medium">Jawab pertanyaan & cek risiko</p>
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white text-brand-600 shadow-md transition group-hover:scale-110">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                        </svg>
                    </span>
                </div>
            </div>
        </a>
    </div>

    {{-- Active Orders / Bookings Tracking Banner on Home --}}
    @if (!empty($activeOrders))
        <section class="space-y-2.5">
            <h2 class="text-sm font-bold text-slate-900 flex items-center gap-1.5">
                🔔 Status Layanan & Pesanan Aktif
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                @foreach ($activeOrders as $ao)
                    <a href="{{ $ao['url'] }}" class="flex items-center justify-between gap-3 rounded-2xl border border-brand-100 bg-gradient-to-r from-brand-50/40 via-white to-blue-50/30 p-3.5 shadow-sm transition hover:scale-[1.01] active:scale-[0.99]">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-600 text-white text-lg shadow-sm">
                                {{ $ao['icon'] }}
                            </span>
                            <div class="min-w-0">
                                <h3 class="text-xs font-bold text-slate-900 truncate">{{ $ao['title'] }}</h3>
                                <p class="text-[11px] font-semibold text-slate-600 truncate mt-0.5">{{ $ao['status'] }}</p>
                            </div>
                        </div>
                        <span class="shrink-0 rounded-full bg-brand-600 px-3 py-1.5 text-[9px] font-bold text-white shadow-xs">
                            Lacak →
                        </span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Fitur Unggulan --}}
    <section>
        <h2 class="mb-3 text-base font-bold text-slate-900">Fitur Unggulan</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            @foreach ($features as $feature)
                <a
                    href="{{ $feature['url'] }}"
                    class="group flex min-h-[7.5rem] flex-col items-center rounded-2xl border border-brand-50 bg-white px-2 py-3 shadow-sm transition hover:-translate-y-0.5 hover:border-brand-200 hover:shadow-md active:scale-95"
                >
                    <span class="mb-2 flex {{ $feature['icon_class'] ?? 'h-12 w-12' }} shrink-0 items-center justify-center overflow-hidden rounded-2xl transition group-hover:scale-105">
                        <img
                            src="{{ asset($feature['icon']) }}"
                            alt=""
                            class="h-full w-full object-contain {{ $feature['img_class'] ?? '' }}"
                        />
                    </span>
                    <span class="text-center text-xs font-bold leading-snug text-slate-800">{{ $feature['label'] }}</span>
                    <span class="mt-1 text-center text-[11px] leading-snug text-slate-500">{{ $feature['desc'] }}</span>
                </a>
            @endforeach
        </div>
    </section>

    {{-- Promo Banner & Tips Chatbot (Side-by-side on laptop view) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-stretch">
        {{-- Promo banner --}}
        <a
            href="{{ route('education.index') }}"
            class="group relative flex flex-col sm:flex-row items-center justify-between gap-3 overflow-hidden rounded-3xl bg-gradient-to-r from-violet-50 via-purple-50 to-violet-100/80 p-4 shadow-sm ring-1 ring-violet-100 transition hover:shadow-md active:scale-[0.99] h-full"
        >
            <div class="flex items-center gap-3 min-w-0 flex-1">
                <div class="flex h-14 w-12 shrink-0 items-center justify-center">
                    <img
                        src="{{ asset('images/nurse.png') }}"
                        alt=""
                        class="max-h-14 w-full object-contain object-center drop-shadow-sm"
                    />
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-bold text-slate-900">Jaga kesehatan sejak dini ✨</p>
                    <p class="mt-0.5 text-xs leading-relaxed text-slate-500">
                        Deteksi dini membantu mencegah komplikasi serius
                    </p>
                    <span class="mt-2 inline-flex items-center gap-1 rounded-full bg-white px-3 py-1.5 text-xs font-semibold text-violet-700 shadow-sm transition group-hover:bg-violet-600 group-hover:text-white">
                        Pelajari Lebih Lanjut
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                    </span>
                </div>
            </div>
            <div class="hidden sm:flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-2xl">
                <img
                    src="{{ asset('images/shield.png') }}"
                    alt=""
                    class="max-h-12 w-full object-contain object-center"
                />
            </div>
        </a>

        {{-- Tips chatbot --}}
        <section class="flex flex-col justify-between overflow-hidden rounded-3xl bg-gradient-to-r from-emerald-50 to-teal-50 ring-1 ring-emerald-100 p-4 h-full">
            <div class="flex items-start gap-3">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center">
                    <img
                        src="{{ asset('images/idea.png') }}"
                        alt=""
                        class="h-full w-full object-contain"
                    />
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-[10px] font-extrabold uppercase tracking-wide text-emerald-700">Tips dari Chatbot</p>
                    <p
                        x-text="tips[tipIndex]"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="mt-1 text-xs leading-relaxed text-slate-700"
                    ></p>
                </div>
            </div>
            <div class="flex justify-center gap-1.5 pt-3">
                <template x-for="(_, i) in tips" :key="i">
                    <button
                        type="button"
                        @click="tipIndex = i"
                        :class="tipIndex === i ? 'w-4 bg-emerald-500' : 'w-1.5 bg-emerald-200'"
                        class="h-1.5 rounded-full transition-all"
                        :aria-label="'Tip ' + (i + 1)"
                    ></button>
                </template>
            </div>
        </section>
    </div>

    {{-- Status kesehatan --}}
    <section>
        <div class="mb-3 flex items-center justify-between">
            <h2 class="text-base font-bold text-slate-900">Status Kesehatan Anda</h2>
            @auth
                <a href="{{ route('dashboard') }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700">Lihat Semua →</a>
            @else
                <a href="{{ route('login') }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700">Masuk →</a>
            @endauth
        </div>
        <div class="grid grid-cols-3 gap-1.5 sm:gap-2">
            @foreach ($healthStatus as $status)
                <div class="rounded-2xl border border-brand-50 bg-white p-2.5 text-center shadow-sm sm:p-3">
                    <span @class(['mx-auto mb-2 flex h-9 w-9 items-center justify-center rounded-xl', $status['bg']])>
                        @if ($status['icon'] === 'heart')
                            <svg class="h-5 w-5 text-rose-500" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219z"/>
                            </svg>
                        @elseif ($status['icon'] === 'bp')
                            <svg class="h-5 w-5 text-violet-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>
                            </svg>
                        @else
                            <svg class="h-5 w-5 text-emerald-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 01-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 006.16-12.12A14.98 14.98 0 009.631 8.41m5.96 5.96a14.926 14.926 0 01-5.841 2.58m-.119-8.54a6 6 0 00-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 00-2.58 5.84"/>
                            </svg>
                        @endif
                    </span>
                    <p class="text-xs font-medium leading-tight text-slate-500">{{ $status['label'] }}</p>
                    <p @class(['mt-0.5 text-xs font-bold sm:text-sm', $status['tone']])>{{ $status['value'] }}</p>
                </div>
            @endforeach
        </div>
    </section>
</div>
@endsection

@push('scripts')
<style>
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-6px); }
    }
    .scrollbar-none::-webkit-scrollbar { display: none; }
    .scrollbar-none { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endpush
