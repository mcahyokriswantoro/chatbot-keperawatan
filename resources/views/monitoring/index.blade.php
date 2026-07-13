@extends('layouts.mobile')

@section('content')
    @php $monitoringCssVer = filemtime(public_path('css/monitoring-choices.css')) ?: time(); @endphp
    <link rel="stylesheet" href="/css/monitoring-choices.css?v={{ $monitoringCssVer }}">

    <x-mobile.page-header title="Monitoring" />

    <x-mobile.alert />

    @if ($diseases === [])
        <div class="overflow-hidden rounded-3xl border border-dashed border-amber-200 bg-gradient-to-br from-amber-50 to-white p-8 text-center shadow-sm">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-100 text-2xl">📋</div>
            <p class="mt-4 text-sm font-bold text-amber-900">Belum ada skrining lanjut</p>
            <p class="mt-2 text-xs leading-relaxed text-amber-800">Selesaikan skrining penyakit terlebih dahulu untuk mengaktifkan monitoring harian dan bulanan.</p>
            <a href="{{ route('detection.start') }}" class="mt-5 inline-flex items-center gap-2 rounded-full bg-brand-600 px-6 py-2.5 text-xs font-semibold text-white shadow-soft">
                Mulai Skrining →
            </a>
        </div>
    @else
        {{-- Hero --}}
        <div class="monitoring-card-hero relative mb-5 overflow-hidden rounded-3xl px-5 py-5 shadow-lg">
            <div class="pointer-events-none absolute -right-8 -top-8 h-32 w-32 rounded-full bg-white/10 blur-2xl"></div>
            <p class="monitoring-card-hero__eyebrow text-[10px] font-semibold uppercase tracking-wider">Self management</p>
            <h1 class="monitoring-card-hero__title mt-1 text-lg font-bold">Bagaimana kabar Anda?</h1>
            <p class="monitoring-card-hero__label mt-1 text-xs leading-relaxed">Ceritakan kondisi harian dan lihat perkembangan Anda dari waktu ke waktu</p>
        </div>

        <div
            x-data="{ tab: 'harian', disease: '{{ $diseases[0]['slug'] }}' }"
            class="space-y-4"
        >
            {{-- Tab --}}
            <div class="grid grid-cols-2 gap-1 rounded-2xl bg-slate-200/70 p-1 shadow-inner">
                <button
                    type="button"
                    @click="tab = 'harian'"
                    :class="tab === 'harian' ? 'bg-brand-600 text-white shadow-md ring-2 ring-brand-200' : 'text-slate-600 hover:text-slate-800'"
                    class="flex items-center justify-center gap-1.5 rounded-xl py-3 text-xs font-bold transition"
                >
                    <span>📅</span> Harian
                </button>
                <button
                    type="button"
                    @click="tab = 'bulanan'"
                    :class="tab === 'bulanan' ? 'bg-brand-600 text-white shadow-md ring-2 ring-brand-200' : 'text-slate-600 hover:text-slate-800'"
                    class="flex items-center justify-center gap-1.5 rounded-xl py-3 text-xs font-bold transition"
                >
                    <span>📊</span> Bulanan
                </button>
            </div>

            {{-- Disease picker --}}
            <div>
                <p class="mb-2 text-[10px] font-bold uppercase tracking-wide text-slate-400">Pilih Penyakit</p>
                <div class="flex gap-2 overflow-x-auto pb-1 [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                    @foreach ($diseases as $item)
                        <button
                            type="button"
                            @click="disease = '{{ $item['slug'] }}'"
                            :class="disease === '{{ $item['slug'] }}'
                                ? 'border-brand-600 bg-brand-600 text-white shadow-md ring-2 ring-brand-200'
                                : 'border-transparent bg-slate-100 text-slate-600 hover:bg-slate-50'"
                            class="flex shrink-0 flex-col items-start gap-1 rounded-2xl border px-3.5 py-2.5 text-left transition"
                        >
                            <span class="text-lg leading-none">{{ $item['icon'] }}</span>
                            <span class="max-w-[7rem] truncate text-[11px] font-bold leading-tight">{{ $item['label'] }}</span>
                            <span class="rounded-full px-1.5 py-0.5 text-[9px] font-semibold" :class="disease === '{{ $item['slug'] }}' ? 'bg-white/20 text-white' : 'bg-brand-50 text-brand-700'">{{ $item['risk'] }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            @foreach ($diseases as $item)
                <div x-show="tab === 'harian' && disease === '{{ $item['slug'] }}'" x-cloak class="space-y-4">
                    @include('monitoring.partials.daily-form', [
                        'disease' => $item['slug'],
                        'diseaseInfo' => $item,
                        'userMedications' => $userMedications,
                        'severityOptions' => $severityOptions,
                        'selfManagementOptions' => $selfManagementOptions,
                    ])

                    @include('monitoring.partials.daily-results', [
                        'disease' => $item['slug'],
                        'summary' => $dailySummaries[$item['slug']],
                        'chartData' => $chartData[$item['slug']] ?? [],
                    ])
                </div>

                <div x-show="tab === 'bulanan' && disease === '{{ $item['slug'] }}'" x-cloak>
                    @include('monitoring.partials.monthly-form', [
                        'disease' => $item['slug'],
                        'diseaseInfo' => $item,
                        'preview' => $monthlyPreviews[$item['slug']],
                        'relapseOptions' => $relapseOptions,
                        'currentMonth' => $currentMonth,
                    ])
                </div>
            @endforeach
        </div>
    @endif

    <div class="monitoring-history-section">
        <div class="mb-3 flex items-center justify-between gap-2">
            <h2 class="text-sm font-bold text-slate-900">Riwayat catatan</h2>
            <span class="text-[10px] font-medium text-slate-400">{{ $records->total() }} entri</span>
        </div>
        @forelse ($records as $record)
            @include('monitoring.partials.history-card', ['record' => $record])
        @empty
            <div class="rounded-2xl border border-dashed border-slate-200 bg-white py-10 text-center">
                <p class="text-2xl">📝</p>
                <p class="mt-2 text-sm font-medium text-slate-600">Belum ada data</p>
                <p class="mt-1 text-xs text-slate-400">Catatan harian dan bulanan akan muncul di sini</p>
            </div>
        @endforelse
        <div class="mt-4">{{ $records->links() }}</div>
    </div>
@endsection

@push('scripts')
<style>[x-cloak]{display:none!important}</style>
@endpush
