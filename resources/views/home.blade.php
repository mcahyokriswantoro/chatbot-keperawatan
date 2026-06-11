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
            'label' => 'Edukasi Kesehatan',
            'desc' => 'Artikel & tips kesehatan',
            'url' => route('education.index'),
            'icon' => 'images/unggulan_edukasi.png',
        ],
        [
            'label' => 'Konsultasi Langsung',
            'desc' => 'Tanya langsung ke chatbot',
            'url' => route('help'),
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
    {{-- Hero header --}}
    <header class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-50 via-white to-brand-100/80 px-4 pb-4 pt-4 pr-16 shadow-soft ring-1 ring-brand-100/60">
        <div class="flex items-start gap-3">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-full bg-white shadow-sm ring-2 ring-brand-100">
                <x-app.medical-note-icon class="h-8 w-8" />
            </div>
            <div class="min-w-0 flex-1 pt-0.5">
                <p class="text-xs font-medium text-slate-500">Hi, Saya Chatbot 👋</p>
                <h1 class="text-lg font-bold leading-tight text-slate-900 sm:text-xl">Keperawatan Pintar</h1>
                <p class="mt-1 text-xs leading-relaxed text-slate-500">
                    Saya siap membantu deteksi kesehatan Anda hari ini 💙
                </p>
            </div>
        </div>
        <div class="pointer-events-none absolute right-2 top-2 h-16 w-16 animate-[float_3s_ease-in-out_infinite] sm:h-20 sm:w-20">
            <img
                src="{{ asset('images/robot.png') }}?v={{ filemtime(public_path('images/robot.png')) }}"
                alt=""
                width="80"
                height="80"
                class="h-full w-full object-contain drop-shadow-md"
            />
        </div>
    </header>

    {{-- Main CTA --}}
    <a
        href="{{ $detectionUrl }}"
        class="group relative block overflow-hidden rounded-3xl bg-gradient-to-r from-brand-600 via-brand-500 to-brand-600 p-5 shadow-lg shadow-brand-600/25 transition active:scale-[0.98] hover:shadow-xl hover:shadow-brand-600/30"
    >
        <div class="pointer-events-none absolute inset-0 opacity-20">
            <div class="absolute -right-8 -top-8 h-32 w-32 rounded-full bg-white"></div>
            <div class="absolute -bottom-6 left-1/3 h-24 w-24 rounded-full bg-white/60"></div>
        </div>
        <div class="relative flex items-center gap-4">
            <div class="min-w-0 flex-1">
                <h2 class="text-lg font-bold text-white">Mulai Deteksi Kesehatan</h2>
                <p class="mt-1 text-xs leading-relaxed text-blue-100">
                    Jawab beberapa pertanyaan singkat dan dapatkan hasil deteksi Anda
                </p>
            </div>
            <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-white text-brand-600 shadow-lg transition group-hover:scale-110">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                </svg>
            </span>
        </div>
    </a>

    {{-- Fitur Unggulan --}}
    <section>
        <h2 class="mb-3 text-base font-bold text-slate-900">Fitur Unggulan</h2>
        <div class="grid grid-cols-2 gap-3">
            @foreach ($features as $feature)
                <a
                    href="{{ $feature['url'] }}"
                    class="group flex min-h-[7.5rem] flex-col items-center rounded-2xl border border-brand-50 bg-white px-2 py-3 shadow-sm transition hover:-translate-y-0.5 hover:border-brand-200 hover:shadow-md active:scale-95"
                >
                    <span class="mb-2 flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-2xl transition group-hover:scale-105">
                        <img
                            src="{{ asset($feature['icon']) }}"
                            alt=""
                            class="h-full w-full object-contain"
                        />
                    </span>
                    <span class="text-center text-xs font-bold leading-snug text-slate-800">{{ $feature['label'] }}</span>
                    <span class="mt-1 text-center text-[11px] leading-snug text-slate-500">{{ $feature['desc'] }}</span>
                </a>
            @endforeach
        </div>
    </section>

    {{-- Promo banner --}}
    <a
        href="{{ route('education.index') }}"
        class="group relative flex flex-col gap-3 overflow-hidden rounded-3xl bg-gradient-to-r from-violet-50 via-purple-50 to-violet-100/80 p-4 shadow-sm ring-1 ring-violet-100 transition hover:shadow-md active:scale-[0.99] sm:flex-row sm:items-center"
    >
        <div class="flex items-center gap-3 sm:min-w-0 sm:flex-1">
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
        <div class="hidden h-14 w-12 shrink-0 items-center justify-center overflow-hidden rounded-2xl sm:flex">
            <img
                src="{{ asset('images/shield.png') }}"
                alt=""
                class="max-h-12 w-full object-contain object-center"
            />
        </div>
    </a>

    {{-- Tips hari ini --}}
    <section class="overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-50 to-teal-50 ring-1 ring-emerald-100">
        <div class="flex items-center gap-3 p-4">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center">
                <img
                    src="{{ asset('images/idea.png') }}"
                    alt=""
                    class="h-full w-full object-contain"
                />
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-[10px] font-bold uppercase tracking-wide text-emerald-700">Tips Minggu Ini</p>
                <p class="text-[10px] text-emerald-600/80">Sumber: ayosehat.kemkes.go.id</p>
                <p
                    x-text="tips[tipIndex]"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="mt-0.5 text-xs leading-relaxed text-slate-700"
                ></p>
            </div>
        </div>
        <div class="flex justify-center gap-1.5 pb-3">
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
