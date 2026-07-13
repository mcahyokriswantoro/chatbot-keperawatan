@extends('layouts.mobile')

@section('content')
    <x-mobile.page-header title="Self Management" />

    <x-mobile.alert />

    @if (session('error'))
        <div class="mb-4 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
            {{ session('error') }}
        </div>
    @endif

    <p class="mb-5 text-sm leading-relaxed text-slate-600">
        Daftar kondisi kesehatan di bawah sebagai edukasi. Untuk pemantauan personal, pengingat obat, dan rekomendasi yang disesuaikan, Anda perlu menyelesaikan skrining terlebih dahulu.
    </p>

    @if (! $hasScreening)
        <a
            href="{{ route('detection.start') }}"
            class="mb-5 block rounded-2xl bg-brand-600 px-4 py-4 text-center text-sm font-semibold text-white shadow-soft transition hover:bg-brand-700 active:scale-[0.99]"
        >
            Mulai Skrining Kesehatan →
        </a>
    @else
        @if ($latestScreening)
            <div class="mb-5 rounded-2xl border border-brand-100 bg-brand-50/80 p-4">
                <p class="text-xs font-semibold text-brand-800">Skrining terakhir</p>
                <p class="mt-1 text-sm font-bold text-slate-900">
                    {{ $latestScreening->diseaseLabel() }} · {{ $latestScreening->displayRiskLabel() }}
                </p>
                <p class="mt-1 text-xs text-slate-500">{{ $latestScreening->formattedDateTime('d F Y') }}</p>
                <div class="mt-3">
                    <x-screening.tts-button :text="$latestScreening->speechText()" :gender="auth()->user()->gender" class="w-full" />
                </div>
            </div>
        @endif
    @endif

    <h2 class="mb-3 text-sm font-bold text-slate-900">Informasi Kondisi Kesehatan</h2>

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

    @if ($hasScreening)
        <a
            href="{{ route('monitoring') }}"
            class="mt-6 block rounded-2xl border border-dashed border-brand-200 bg-brand-50 px-4 py-3 text-center text-sm font-semibold text-brand-700"
        >
            Catat monitoring harian →
        </a>
    @else
        <p class="mt-6 rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-3 text-center text-xs text-slate-500">
            Pemantauan kesehatan dan rekomendasi personal terbuka setelah skrining selesai.
        </p>
    @endif
@endsection
