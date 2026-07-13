@extends('layouts.mobile')

@section('content')
    <x-mobile.page-header title="Detail Skrining" :back="route('history')" />

    @php
        $theme = $session->riskTheme();
        $score = $session->scoreData();
        $guide = $session->selfManagementGuideBlock();
    @endphp

    {{-- Kartu hasil utama --}}
    <div class="mb-5 overflow-hidden rounded-3xl border bg-white shadow-sm {{ $theme['border'] }}">
        <div @class(['px-5 py-4', $theme['bg']])>
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-500">Hasil Skrining</p>
                    <h1 class="mt-1 text-lg font-bold text-slate-900">{{ $session->diseaseLabel() ?? 'Skrining Kesehatan' }}</h1>
                    <p class="mt-0.5 text-xs text-slate-500">{{ $session->formattedDateTime('d F Y, H:i') }}</p>
                </div>
                <span @class(['shrink-0 rounded-full px-3 py-1 text-xs font-bold uppercase ring-1', $theme['bg'], $theme['text'], $theme['ring']])>
                    {{ $session->historyBadgeLabel() }}
                </span>
            </div>

            @if ($session->isInitialScreening())
                <div class="mt-4">
                    <p @class(['text-sm font-bold', $theme['text']])>{{ $session->scoreLabel() }}</p>
                </div>
            @else
            <div class="mt-4 flex items-end gap-3">
                @if ($score['total'] !== null)
                    <div>
                        <p class="text-[10px] font-medium text-slate-500">Total Skor</p>
                        <p @class(['text-4xl font-bold leading-none', $theme['text']])>
                            {{ $score['total'] }}@if($score['max'])<span class="text-xl font-semibold opacity-60">/{{ $score['max'] }}</span>@endif
                        </p>
                    </div>
                @endif
                <div class="min-w-0 flex-1 pb-1">
                    <p @class(['text-sm font-bold', $theme['text']])>{{ $session->scoreLabel() }}</p>
                    @if ($score['hasil_kategori'] && $score['risiko_label'])
                        <p class="mt-0.5 text-[11px] text-slate-600">Klasifikasi: {{ $score['hasil_kategori'] }}</p>
                    @endif
                </div>
            </div>
            @endif

            @if ($session->showsEmergencyUi())
                <div class="mt-4 rounded-xl border border-rose-300 bg-rose-100 px-3 py-2.5">
                    <p class="text-xs font-bold text-rose-800">⚠ Peringatan Darurat</p>
                    <p class="mt-0.5 text-[11px] leading-relaxed text-rose-900">Gejala yang Anda laporkan memerlukan penanganan segera di fasilitas kesehatan.</p>
                </div>
            @endif

            @if ($session->scoreProgressPercent() !== null)
                <div class="mt-4">
                    <div class="h-2 overflow-hidden rounded-full bg-white/60 ring-1 ring-black/5">
                        <div
                            @class(['h-full rounded-full', $theme['accent']])
                            style="width: {{ $session->scoreProgressPercent() }}%"
                        ></div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="mb-5">
        <x-screening.tts-button :text="$session->speechText()" :gender="auth()->user()->gender" class="w-full" />
    </div>

    {{-- Langkah selanjutnya --}}
    <section class="mb-5 rounded-2xl border border-brand-100 bg-white p-4 shadow-sm">
        <h2 class="text-sm font-bold text-slate-900">
            {{ $session->isInitialScreening() ? 'Tindak Lanjut Skrining' : 'Apa yang harus dilakukan?' }}
        </h2>
        <p class="mt-2 text-sm leading-relaxed text-slate-700">{{ $session->nextStepMessage() }}</p>

        @if ($session->isInitialScreening())
            <div class="mt-4">
                @include('history.partials.initial-recommendations', ['session' => $session])
            </div>
        @elseif ($guide)
            <div class="mt-4 rounded-xl bg-brand-50/80 p-3 ring-1 ring-brand-100">
                <p class="text-xs font-bold text-brand-800">{{ $guide['label'] ?? 'Panduan Self Management' }}</p>
                @foreach ($guide['sections'] ?? [] as $section)
                    <div class="mt-3">
                        <p class="text-[11px] font-semibold text-slate-800">{{ $section['title'] }}</p>
                        <ul class="mt-1.5 space-y-1">
                            @foreach ($section['items'] as $item)
                                <li class="flex gap-2 text-xs leading-relaxed text-slate-600">
                                    <span class="text-brand-400">•</span>
                                    <span>{{ $item }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="mt-4 grid gap-2 sm:grid-cols-2">
            @if (! $session->isInitialScreening() && $session->selfManagementUrl())
                <a
                    href="{{ $session->selfManagementUrl() }}"
                    class="flex items-center justify-center gap-2 rounded-2xl bg-brand-600 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700 active:scale-[0.98]"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Buka Self Management
                </a>
            @endif
            @if ($session->showsEmergencyUi())
                <a
                    href="{{ route('emergency') }}"
                    class="flex items-center justify-center gap-2 rounded-2xl bg-rose-600 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-rose-700"
                >
                    Peringatan Darurat
                </a>
            @endif
            <a
                href="{{ route('monitoring') }}"
                class="flex items-center justify-center gap-2 rounded-2xl border border-brand-200 bg-white py-3 text-sm font-semibold text-brand-600 transition hover:bg-brand-50 {{ ! $session->selfManagementUrl() && ! $session->showsEmergencyUi() ? 'sm:col-span-2' : '' }}"
            >
                Catat Monitoring
            </a>
        </div>
    </section>

    {{-- Jawaban interaktif --}}
    @include('history.partials.answer-breakdown', ['session' => $session])
@endsection
