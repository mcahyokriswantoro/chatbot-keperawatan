@extends('layouts.mobile')

@section('content')
    {{-- Header --}}
    <header class="mb-6 flex items-start gap-4">
        <x-mobile.logo />

        <div class="flex-1 pt-1">
            <h1 class="text-xl font-bold leading-snug text-slate-900">
                Hi, Saya Chatbot<br>Keperawatan Pintar,
            </h1>
            <p class="mt-1 text-sm font-medium text-slate-500">
                Saya Akan Membantu Deteksi<br>Kesehatan Anda
            </p>
        </div>
    </header>

    {{-- CTA Button --}}
    <a
        href="{{ route('detection.identity') }}"
        class="mb-8 flex w-full items-center justify-between rounded-full bg-brand-600 py-4 pl-6 pr-2 text-white shadow-soft transition hover:bg-brand-700 active:scale-[0.98]"
    >
        <span class="text-base font-semibold">Mulai Deteksi Kesehatan</span>
        <span class="flex h-11 w-11 items-center justify-center rounded-full bg-white text-brand-600">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
            </svg>
        </span>
    </a>

    {{-- Featured Section --}}
    <section>
        <h2 class="mb-4 text-lg font-bold text-slate-900">Fitur Unggulan</h2>

        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-b from-brand-200 to-brand-300 shadow-card">
            {{-- Floating icons --}}
            <div class="absolute left-4 top-6 z-10">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-600 shadow-md">
                    <svg class="h-7 w-7 text-white" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-1 14.93V18h2v-2.07A6.001 6.001 0 0012 7a6 6 0 00-1 8.93z"/>
                        <path d="M12 9v4l2.5 1.5" fill="none" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>

            <div class="absolute right-4 top-6 z-10">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-500 shadow-md">
                    <svg class="h-7 w-7 text-white" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                        <path d="M4 12h16M6 9h12M8 15h8" stroke="white" stroke-width="1.2" stroke-linecap="round" fill="none"/>
                    </svg>
                </div>
            </div>

            {{-- Nurse illustration --}}
            <div class="relative flex justify-center px-4 pt-10 pb-4">
                <div class="relative h-52 w-44">
                    {{-- Nurse character SVG --}}
                    <svg class="h-full w-full drop-shadow-lg" viewBox="0 0 180 220" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <ellipse cx="90" cy="200" rx="55" ry="12" fill="rgba(0,80,180,0.15)"/>
                        <path d="M55 95c0-22 15-40 35-40s35 18 35 40v75H55V95z" fill="#f5c6a0"/>
                        <circle cx="90" cy="72" r="32" fill="#f5c6a0"/>
                        <path d="M58 62c2-18 14-28 32-28s30 10 32 28c-8-6-18-9-32-9s-24 3-32 9z" fill="#4a3728"/>
                        <ellipse cx="78" cy="74" rx="4" ry="5" fill="#2d2d2d"/>
                        <ellipse cx="102" cy="74" rx="4" ry="5" fill="#2d2d2d"/>
                        <path d="M82 88c4 4 12 4 16 0" stroke="#c97b5a" stroke-width="2" stroke-linecap="round" fill="none"/>
                        <path d="M70 170h40l-5 30H75l-5-30z" fill="#fff"/>
                        <path d="M55 95l-20 75h30l5-40-15-35zM125 95l20 75h-30l-5-40 15-35z" fill="#fff"/>
                        <path d="M60 95h60v20c0 8-6 14-14 14h-32c-8 0-14-6-14-14V95z" fill="#0066FF"/>
                        <rect x="72" y="48" width="36" height="18" rx="4" fill="white"/>
                        <path d="M78 52h24" stroke="#0066FF" stroke-width="2"/>
                        <rect x="80" y="42" width="20" height="8" rx="2" fill="white"/>
                        <path d="M85 95v8M95 95v8" stroke="#0066FF" stroke-width="3" stroke-linecap="round"/>
                        <path d="M100 120l25-35" stroke="#f5c6a0" stroke-width="12" stroke-linecap="round"/>
                        <circle cx="128" cy="78" r="8" fill="#f5c6a0"/>
                        <path d="M128 70l3-8 3 8" stroke="#0066FF" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>

            {{-- Info card --}}
            <a href="{{ route('detection.identity') }}" class="mx-4 mb-4 block rounded-2xl bg-white px-5 py-4 text-center shadow-soft transition hover:shadow-md active:scale-[0.99]">
                <h3 class="text-base font-bold text-slate-900">Deteksi Kesehatan</h3>
                <p class="mt-1 text-sm leading-relaxed text-slate-500">
                    Jawab beberapa pertanyaan dan dapatkan hasil deteksi Anda
                </p>
            </a>
        </div>
    </section>
@endsection
