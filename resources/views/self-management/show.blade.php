@extends('layouts.mobile')

@section('content')
    <x-mobile.page-header :title="'Self Management '.$label" :back="route('self-management')" />

    <div class="mb-5 flex items-center gap-3 rounded-2xl bg-gradient-to-r from-brand-50 to-white p-4 ring-1 ring-brand-100">
        <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white text-3xl shadow-sm">{{ $icon }}</span>
        <div class="min-w-0 flex-1">
            <h1 class="text-lg font-bold text-slate-900">{{ $label }}</h1>
            <p class="mt-1 text-xs leading-relaxed text-slate-600">
                Panduan ini membantu Anda merawat diri di rumah berdasarkan tingkat risiko. Ini bukan pengganti pemeriksaan dokter.
            </p>
            @if ($recommendedRisk)
                <p class="mt-2 text-xs font-semibold text-brand-700">
                    Berdasarkan skrining terakhir Anda: risiko {{ strtolower($recommendedRisk) }}.
                    @if ($latestScreening)
                        ({{ $latestScreening->created_at->format('d M Y') }})
                    @endif
                </p>
            @endif
        </div>
    </div>

    <x-self-management.guide :guide="$guide" :highlight="$recommendedRisk" />

    <div class="mt-6 grid grid-cols-2 gap-2">
        <a
            href="{{ route('detection.chat', $disease) }}"
            class="rounded-2xl border border-brand-100 bg-white py-3 text-center text-xs font-semibold text-brand-600 shadow-sm"
        >
            Ulangi Skrining
        </a>
        <a
            href="{{ route('monitoring') }}"
            class="rounded-2xl bg-brand-600 py-3 text-center text-xs font-semibold text-white shadow-soft"
        >
            Catat Monitoring
        </a>
    </div>
@endsection
