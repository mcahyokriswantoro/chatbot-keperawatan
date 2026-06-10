@extends('layouts.mobile')

@section('content')
    <x-mobile.page-header title="Self Management" />

    <p class="mb-5 text-sm leading-relaxed text-slate-600">
        Pilih jenis penyakit untuk melihat panduan perawatan mandiri sesuai tingkat risiko. Panduan ini membantu Anda memahami langkah-langkah yang bisa dilakukan di rumah.
    </p>

    <div class="space-y-3">
        @foreach ($diseases as $item)
            <a
                href="{{ route('self-management.show', $item['key']) }}"
                class="flex items-center gap-4 rounded-2xl border border-brand-100 bg-white p-4 shadow-card transition hover:border-brand-300 hover:shadow-md active:scale-[0.99]"
            >
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-brand-50 text-2xl">
                    {{ $item['icon'] }}
                </span>
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="font-bold text-slate-900">{{ $item['label'] }}</h2>
                        @if ($item['latest_risk'])
                            <span @class([
                                'rounded-full px-2 py-0.5 text-[10px] font-semibold',
                                'bg-rose-100 text-rose-700' => $item['latest_risk'] === 'Tinggi',
                                'bg-amber-100 text-amber-800' => $item['latest_risk'] === 'Sedang',
                                'bg-emerald-100 text-emerald-700' => $item['latest_risk'] === 'Rendah',
                            ])>
                                Skrining: {{ $item['latest_risk'] }}
                            </span>
                        @endif
                    </div>
                    <p class="mt-0.5 text-xs text-slate-500">{{ $item['description'] }}</p>
                </div>
                <svg class="h-5 w-5 shrink-0 text-brand-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                </svg>
            </a>
        @endforeach
    </div>

    <a
        href="{{ route('monitoring') }}"
        class="mt-6 block rounded-2xl border border-dashed border-brand-200 bg-brand-50 px-4 py-3 text-center text-sm font-semibold text-brand-700"
    >
        Catat monitoring harian →
    </a>
@endsection
