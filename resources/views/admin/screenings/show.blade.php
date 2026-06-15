@extends('layouts.admin')

@section('title', 'Detail Skrining')

@section('content')
    @php
        $theme = $session->riskTheme();
        $score = $session->scoreData();
        $identity = $session->identity;
        $ttsGender = $session->user?->gender ?? $identity?->gender;
    @endphp

    <x-admin.page-banner
        :title="$session->diseaseLabel() ?? 'Skrining'"
        :subtitle="$session->created_at->translatedFormat('d F Y, H:i')"
        :back="route('admin.screenings.index')"
        tone="emerald"
        :show-actions="false"
    />

    <div class="mb-5 overflow-hidden rounded-2xl border bg-white shadow-sm {{ $theme['border'] }}">
        <div @class(['px-4 py-4', $theme['bg']])>
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-[10px] font-semibold uppercase text-slate-500">Hasil</p>
                    @if ($score['total'] !== null)
                        <p @class(['mt-1 text-3xl font-bold', $theme['text']])>
                            {{ $score['total'] }}@if($score['max'])<span class="text-lg opacity-60">/{{ $score['max'] }}</span>@endif
                        </p>
                    @endif
                    <p @class(['mt-1 text-sm font-bold', $theme['text']])>{{ $session->scoreLabel() }}</p>
                </div>
                @include('admin.partials.risk-badge', ['session' => $session])
            </div>
            @if ($session->showsEmergencyUi())
                <div class="mt-4 rounded-xl border border-rose-300 bg-rose-100 px-3 py-2.5 text-xs text-rose-900">
                    <strong>Peringatan darurat</strong> — disarankan penanganan segera di fasilitas kesehatan.
                </div>
            @endif
            @if ($session->summary)
                <p class="mt-4 text-xs leading-relaxed text-slate-700 whitespace-pre-wrap">{{ $session->summary }}</p>
            @endif
        </div>
    </div>

    <section class="mb-5 rounded-2xl border border-brand-100 bg-white p-4 shadow-sm">
        <h2 class="text-sm font-bold text-slate-900">Panduan Self-Management (Audio)</h2>
        <p class="mt-1 text-xs text-slate-500">Dengarkan rekomendasi sesuai tingkat risiko skrining ini.</p>
        <div class="mt-3">
            <x-screening.tts-button :text="$session->speechText()" :gender="$ttsGender" class="w-full" />
        </div>
    </section>

    @if ($session->user || $identity)
        <section class="mb-5 space-y-3">
            @if ($session->user)
                <div class="rounded-2xl border border-brand-100 bg-white p-4 shadow-sm">
                    <p class="text-[10px] font-semibold uppercase text-slate-500">Akun pengguna</p>
                    <p class="mt-1 font-bold text-slate-900">{{ $session->user->name }}</p>
                    <p class="text-xs text-slate-500">{{ $session->user->email }}</p>
                    <a href="{{ route('admin.users.show', $session->user) }}" class="mt-2 inline-block text-xs font-semibold text-brand-600">Lihat profil →</a>
                </div>
            @endif
            @if ($identity)
                <div class="rounded-2xl border border-brand-100 bg-white p-4 shadow-sm">
                    <p class="text-[10px] font-semibold uppercase text-slate-500">Identitas subjek</p>
                    <dl class="mt-2 space-y-2 text-sm">
                        <div class="flex justify-between gap-3"><dt class="text-slate-500">Nama</dt><dd class="font-medium">{{ $identity->name }}</dd></div>
                        <div class="flex justify-between gap-3"><dt class="text-slate-500">Target</dt><dd>{{ $identity->screening_target === 'other' ? 'Orang lain' : 'Diri sendiri' }}</dd></div>
                        <div class="flex justify-between gap-3"><dt class="text-slate-500">Usia</dt><dd>{{ $identity->age ? $identity->age.' th' : '—' }}</dd></div>
                        <div class="flex justify-between gap-3"><dt class="text-slate-500">Telepon</dt><dd>{{ $identity->phone ?? '—' }}</dd></div>
                    </dl>
                </div>
            @endif
        </section>
    @endif

    @include('history.partials.answer-breakdown', ['session' => $session])
@endsection
