@extends('layouts.mobile')

@php
    $detectionUrl = auth()->check()
        ? route('detection.identity')
        : route('login');
    $historyUrl = auth()->check() ? route('history') : route('login');

    $features = [
        [
            'label' => 'Deteksi Kesehatan',
            'desc' => 'Skrining gejala interaktif',
            'url' => $detectionUrl,
            'icon_bg' => 'bg-blue-50',
            'icon_text' => 'text-brand-600',
            'icon' => 'shield',
        ],
        [
            'label' => 'Riwayat Kesehatan',
            'desc' => 'Lihat hasil skrining',
            'url' => $historyUrl,
            'icon_bg' => 'bg-teal-50',
            'icon_text' => 'text-teal-600',
            'icon' => 'history',
        ],
        [
            'label' => 'Edukasi Kesehatan',
            'desc' => 'Artikel & tips kesehatan',
            'url' => route('education.index'),
            'icon_bg' => 'bg-violet-50',
            'icon_text' => 'text-violet-600',
            'icon' => 'education',
        ],
        [
            'label' => 'Konsultasi Langsung',
            'desc' => 'Bantuan & layanan darurat',
            'url' => route('help'),
            'icon_bg' => 'bg-orange-50',
            'icon_text' => 'text-orange-600',
            'icon' => 'help',
        ],
    ];

    $healthStatus = [
        ['label' => 'Kesehatan Umum', 'value' => 'Baik', 'tone' => 'text-emerald-600', 'bg' => 'bg-rose-50', 'icon' => 'heart'],
        ['label' => 'Kualitas Tidur', 'value' => 'Cukup', 'tone' => 'text-amber-600', 'bg' => 'bg-violet-50', 'icon' => 'moon'],
        ['label' => 'Aktivitas', 'value' => 'Aktif', 'tone' => 'text-emerald-600', 'bg' => 'bg-emerald-50', 'icon' => 'activity'],
    ];

    $tips = [
        'Minum minimal 8 gelas air per hari untuk menjaga hidrasi tubuh.',
        'Olahraga ringan 30 menit sehari membantu menjaga kesehatan jantung.',
        'Cek tekanan darah secara berkala, terutama jika berusia di atas 40 tahun.',
        'Istirahat cukup 7–8 jam per malam untuk pemulihan tubuh yang optimal.',
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
    <header class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-50 via-white to-brand-100/80 px-4 pb-4 pt-4 shadow-soft ring-1 ring-brand-100/60">
        <div class="flex items-start gap-3">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-full bg-white shadow-sm ring-2 ring-brand-100">
                <img src="{{ asset('images/medical_report.png') }}" alt="" class="h-8 w-8 object-contain" />
            </div>
            <div class="min-w-0 flex-1 pt-0.5">
                <p class="text-xs font-medium text-slate-500">Hi, Saya Chatbot 👋</p>
                <h1 class="text-xl font-bold leading-tight text-slate-900">Keperawatan Pintar</h1>
                <p class="mt-1 text-xs leading-relaxed text-slate-500">
                    Saya siap membantu deteksi kesehatan Anda hari ini 💙
                </p>
            </div>
            <div class="relative h-20 w-20 shrink-0 animate-[float_3s_ease-in-out_infinite]">
                <svg class="h-full w-full drop-shadow-md" viewBox="0 0 80 90" fill="none" aria-hidden="true">
                    <ellipse cx="40" cy="82" rx="28" ry="5" fill="rgba(0,102,255,0.12)"/>
                    <rect x="18" y="28" width="44" height="48" rx="14" fill="#0066FF"/>
                    <rect x="24" y="36" width="32" height="24" rx="8" fill="#fff"/>
                    <circle cx="32" cy="46" r="4" fill="#0066FF"/>
                    <circle cx="48" cy="46" r="4" fill="#0066FF"/>
                    <path d="M34 54h12" stroke="#0066FF" stroke-width="2" stroke-linecap="round"/>
                    <rect x="30" y="14" width="20" height="12" rx="4" fill="#0066FF"/>
                    <circle cx="40" cy="10" r="6" fill="#0066FF"/>
                    <path d="M12 40l8-6M68 40l-8-6" stroke="#0066FF" stroke-width="4" stroke-linecap="round"/>
                </svg>
            </div>
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
                <div class="mt-3 flex items-center gap-2">
                    <div class="flex -space-x-2">
                        @foreach (['bg-brand-300', 'bg-teal-300', 'bg-violet-300'] as $dot)
                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full {{ $dot }} ring-2 ring-brand-600 text-[8px] font-bold text-white">👤</span>
                        @endforeach
                    </div>
                    <span class="text-[10px] font-medium text-blue-100">12.458+ pengguna telah mencoba</span>
                </div>
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
        <div class="grid grid-cols-4 gap-2">
            @foreach ($features as $feature)
                <a
                    href="{{ $feature['url'] }}"
                    class="group flex flex-col items-center rounded-2xl border border-brand-50 bg-white px-1.5 py-3 shadow-sm transition hover:-translate-y-0.5 hover:border-brand-200 hover:shadow-md active:scale-95"
                >
                    <span @class([
                        'mb-2 flex h-11 w-11 items-center justify-center rounded-2xl transition group-hover:scale-105',
                        $feature['icon_bg'],
                        $feature['icon_text'],
                    ])>
                        @if ($feature['icon'] === 'shield')
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.5h10.5a2.25 2.25 0 012.25 2.25v7.5a2.25 2.25 0 01-2.25 2.25H6.75a2.25 2.25 0 01-2.25-2.25v-7.5A2.25 2.25 0 016.75 4.5z"/>
                            </svg>
                        @elseif ($feature['icon'] === 'history')
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75l2.25 2.25L15 9.75"/>
                            </svg>
                        @elseif ($feature['icon'] === 'education')
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3-9h3.75m-3.75 3h3.75m-3.75 3h3.75M6.75 4.5h10.5a1.5 1.5 0 011.5 1.5v12a1.5 1.5 0 01-1.5 1.5H6.75a1.5 1.5 0 01-1.5-1.5v-12a1.5 1.5 0 011.5-1.5z"/>
                            </svg>
                        @else
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a3.375 3.375 0 106.75 0 3.375 3.375 0 00-6.75 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75c0-1.243 2.01-3.75 9.75-3.75s9.75 2.507 9.75 3.75"/>
                            </svg>
                        @endif
                    </span>
                    <span class="text-center text-[8px] font-bold leading-tight text-slate-800">{{ $feature['label'] }}</span>
                </a>
            @endforeach
        </div>
    </section>

    {{-- Promo banner --}}
    <a
        href="{{ route('education.index') }}"
        class="group relative flex items-center gap-3 overflow-hidden rounded-3xl bg-gradient-to-r from-violet-50 via-purple-50 to-violet-100/80 p-4 shadow-sm ring-1 ring-violet-100 transition hover:shadow-md active:scale-[0.99]"
    >
        <div class="relative h-20 w-16 shrink-0">
            <svg class="h-full w-full drop-shadow-sm" viewBox="0 0 80 100" fill="none" aria-hidden="true">
                <ellipse cx="40" cy="92" rx="28" ry="6" fill="rgba(139,92,246,0.15)"/>
                <circle cx="40" cy="32" r="18" fill="#f5c6a0"/>
                <path d="M28 28c2-10 10-16 20-16s18 6 20 16c-6-4-14-6-20-6s-14 2-20 6z" fill="#4a3728"/>
                <path d="M22 55h36l-4 35H26l-4-35z" fill="#fff"/>
                <path d="M26 55h28v12c0 6-5 10-10 10h-8c-5 0-10-4-10-10V55z" fill="#7c3aed"/>
                <rect x="32" y="18" width="16" height="8" rx="2" fill="white"/>
            </svg>
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-sm font-bold text-slate-900">Jaga kesehatan sejak dini ✨</p>
            <p class="mt-0.5 text-[11px] leading-relaxed text-slate-500">
                Deteksi dini membantu mencegah komplikasi serius
            </p>
            <span class="mt-2 inline-flex items-center gap-1 rounded-full bg-white px-3 py-1.5 text-[11px] font-semibold text-violet-700 shadow-sm transition group-hover:bg-violet-600 group-hover:text-white">
                Pelajari Lebih Lanjut
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
            </span>
        </div>
        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-violet-100 text-violet-600">
            <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.5h10.5a2.25 2.25 0 012.25 2.25v7.5a2.25 2.25 0 01-2.25 2.25H6.75a2.25 2.25 0 01-2.25-2.25v-7.5A2.25 2.25 0 016.75 4.5z"/>
            </svg>
        </div>
    </a>

    {{-- Tips hari ini --}}
    <section class="overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-50 to-teal-50 ring-1 ring-emerald-100">
        <div class="flex items-center gap-3 p-4">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-500 text-white shadow-sm">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 001.5-.189m-1.5.189a6.01 6.01 0 01-1.5-.189m1.5.189V12m0 0a6.01 6.01 0 00-1.5-.189M12 12a6.01 6.01 0 011.5-.189M12 12V6.75"/>
                </svg>
            </span>
            <div class="min-w-0 flex-1">
                <p class="text-[10px] font-bold uppercase tracking-wide text-emerald-700">Tips Hari Ini</p>
                <p
                    x-text="tips[tipIndex]"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="mt-0.5 text-xs leading-relaxed text-slate-700"
                ></p>
            </div>
            <div class="shrink-0 text-right">
                <span class="text-2xl" aria-hidden="true">💧</span>
                <p class="text-[10px] font-medium text-slate-400">{{ now()->format('H:i') }}</p>
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
        <div class="-mx-1 flex gap-3 overflow-x-auto px-1 pb-1 scrollbar-none">
            @foreach ($healthStatus as $status)
                <div class="min-w-[120px] shrink-0 rounded-2xl border border-brand-50 bg-white p-3 shadow-sm transition hover:shadow-md">
                    <span @class(['mb-2 flex h-9 w-9 items-center justify-center rounded-xl', $status['bg']])>
                        @if ($status['icon'] === 'heart')
                            <svg class="h-5 w-5 text-rose-500" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219z"/>
                            </svg>
                        @elseif ($status['icon'] === 'moon')
                            <svg class="h-5 w-5 text-violet-500" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M9.528 1.718a.75.75 0 01.162.819A8.97 8.97 0 009 6a9 9 0 009 9 8.97 8.97 0 003.463-.69.75.75 0 01.981.98 10.503 10.503 0 01-9.694 6.46c-5.799 0-10.5-4.701-10.5-10.5 0-4.368 2.667-8.112 6.46-9.694a10.503 10.503 0 016.46 1.698z" clip-rule="evenodd"/>
                            </svg>
                        @else
                            <svg class="h-5 w-5 text-emerald-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 01-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 006.16-12.12A14.98 14.98 0 009.631 8.41m5.96 5.96a14.926 14.926 0 01-5.841 2.58m-.119-8.54a6 6 0 00-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 00-2.58 5.84"/>
                            </svg>
                        @endif
                    </span>
                    <p class="text-[10px] font-medium text-slate-500">{{ $status['label'] }}</p>
                    <p @class(['mt-0.5 text-sm font-bold', $status['tone']])>{{ $status['value'] }}</p>
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
