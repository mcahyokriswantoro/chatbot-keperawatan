@extends('layouts.mobile')

@section('content')
    <x-mobile.page-header :title="'Self Management '.$label" :back="route('self-management')" />

    <x-mobile.alert />

    <div class="mb-5 flex items-center gap-3 rounded-2xl bg-gradient-to-r from-brand-50 to-white p-4 ring-1 ring-brand-100">
        <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white text-3xl shadow-sm">{{ $icon }}</span>
        <div class="min-w-0 flex-1">
            <h1 class="text-lg font-bold text-slate-900">{{ $label }}</h1>
            <p class="mt-1 text-xs leading-relaxed text-slate-600">{{ $description }}</p>
        </div>
    </div>

    @if (! $hasScreening)
        <section class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 p-4">
            <h2 class="text-sm font-bold text-amber-900">Skrining diperlukan</h2>
            <p class="mt-2 text-sm leading-relaxed text-amber-950">
                Informasi edukasi {{ $label }} ditampilkan di halaman ini. Untuk rekomendasi personal, pemantauan, dan pengingat obat, lakukan skrining terlebih dahulu.
            </p>
            <a
                href="{{ route('detection.chat', $disease) }}"
                class="mt-4 block rounded-2xl bg-brand-600 py-3 text-center text-sm font-semibold text-white shadow-soft"
            >
                Mulai Skrining {{ $label }} →
            </a>
        </section>
    @else
        @php $theme = $latestScreening->riskTheme(); @endphp

        <section class="mb-5 overflow-hidden rounded-3xl border bg-white shadow-sm {{ $theme['border'] }}">
            <div @class(['px-5 py-4', $theme['bg']])>
                <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-500">Hasil Skrining Anda</p>
                <div class="mt-2 flex items-start justify-between gap-3">
                    <div>
                        <p class="text-lg font-bold text-slate-900">{{ $latestScreening->scoreLabel() }}</p>
                        <p class="mt-0.5 text-xs text-slate-600">{{ $latestScreening->formattedDateTime('d F Y, H:i') }}</p>
                    </div>
                    <span @class(['shrink-0 rounded-full px-3 py-1 text-xs font-bold uppercase ring-1', $theme['bg'], $theme['text'], $theme['ring']])>
                        {{ $latestScreening->displayRiskLabel() }}
                    </span>
                </div>
                <p class="mt-3 text-sm leading-relaxed text-slate-700">{{ $latestScreening->nextStepMessage() }}</p>
            </div>
        </section>

        <div class="mb-5">
            <x-screening.tts-button :text="$latestScreening->speechText()" :gender="$userGender" class="w-full" />
        </div>

        <h2 class="mb-3 text-sm font-bold text-slate-900">Rekomendasi Personal</h2>
        <x-self-management.guide :guide="$guide" :highlight="$recommendedRisk" />

        <section class="mb-5 rounded-2xl border border-brand-100 bg-white p-4 shadow-sm">
            <h2 class="text-sm font-bold text-slate-900">Pengingat &amp; Aktivitas</h2>
            <p class="mt-1 text-xs leading-relaxed text-slate-500">
                Catat kepatuhan obat, keluhan, diet, dan aktivitas kesehatan harian melalui halaman monitoring.
            </p>
            <a
                href="{{ route('monitoring') }}"
                class="mt-4 flex w-full items-center justify-center gap-2 rounded-2xl bg-brand-600 py-3 text-sm font-semibold text-white shadow-soft transition hover:bg-brand-700 active:scale-[0.98]"
            >
                Buka Monitoring →
            </a>
        </section>

        <a
            href="{{ route('detection.chat', $disease) }}"
            class="block rounded-2xl border border-brand-100 bg-white py-3 text-center text-xs font-semibold text-brand-600 shadow-sm"
        >
            Ulangi Skrining
        </a>
    @endif
@endsection
