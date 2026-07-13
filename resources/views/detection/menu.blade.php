@extends('layouts.mobile')

@section('content')
    <x-mobile.page-header title="Skrining Lanjut" />

    <p class="mb-3 text-sm leading-relaxed text-slate-500">
        Pilih jenis penyakit untuk skrining lanjut. Chatbot akan menanyakan gejala dan faktor risiko yang relevan.
    </p>

    <a
        href="{{ route('home') }}"
        class="mb-5 inline-flex items-center gap-1 text-xs font-semibold text-brand-600 hover:text-brand-700"
    >
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
        </svg>
        Kembali ke beranda
    </a>

    @php
        $order = ['tb_paru', 'dhf', 'ppok', 'penyakit_ginjal', 'stroke', 'jantung_koroner', 'diabetes_melitus', 'hipertensi', 'rheumatoid_arthritis'];
    @endphp

    <div class="space-y-3">
        @foreach ($order as $slug)
            @php $item = $diseases[$slug] ?? null; @endphp
            @if ($item)
                <a
                    href="{{ route('detection.chat', $slug) }}"
                    class="flex items-center gap-4 rounded-2xl bg-white p-4 shadow-card border border-brand-100 transition hover:border-brand-300 hover:shadow-md active:scale-[0.99]"
                >
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-brand-50 text-2xl">
                        {{ $item['icon'] }}
                    </span>
                    <div class="min-w-0 flex-1">
                        <h2 class="font-bold text-slate-900">{{ $item['label'] }}</h2>
                        <p class="mt-0.5 text-xs text-slate-500">{{ $item['description'] }}</p>
                    </div>
                    <svg class="h-5 w-5 shrink-0 text-brand-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                    </svg>
                </a>
            @endif
        @endforeach
    </div>
@endsection
