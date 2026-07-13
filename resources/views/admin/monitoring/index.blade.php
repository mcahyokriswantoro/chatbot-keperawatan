@extends('layouts.admin')

@section('title', 'Monitoring')

@push('styles')
    @php($monitoringCssVer = filemtime(public_path('css/monitoring-choices.css')) ?: time())
    <link rel="stylesheet" href="/css/monitoring-choices.css?v={{ $monitoringCssVer }}">
@endpush

@section('content')
    <x-admin.page-banner
        title="Monitoring Kesehatan"
        :subtitle="($showMonthlyDetail ?? false)
            ? ($charts['total'].' entri · '.$charts['dailyCount'].' harian · '.$charts['monthlyCount'].' bulanan')
            : 'Pilih periode tanggal untuk menampilkan diagram dan data'"
        tone="violet"
    />

    <form method="GET" class="mb-4 space-y-3 rounded-2xl border border-brand-100 bg-white p-4 shadow-sm">
        <div class="grid gap-3 sm:grid-cols-2">
            <div>
                <label class="mb-1 block text-[11px] font-medium text-slate-500">Jenis</label>
                <select name="type" class="w-full rounded-xl border border-brand-200 bg-white px-3 py-2.5 text-sm">
                    <option value="">Semua jenis</option>
                    <option value="daily" @selected($filters['type'] === 'daily')>Harian</option>
                    <option value="monthly" @selected($filters['type'] === 'monthly')>Bulanan</option>
                </select>
            </div>
            <div>
                <label class="mb-1 block text-[11px] font-medium text-slate-500">Penyakit</label>
                <select name="disease" class="w-full rounded-xl border border-brand-200 bg-white px-3 py-2.5 text-sm">
                    <option value="">Semua penyakit</option>
                    @foreach ($diseases as $slug)
                        <option value="{{ $slug }}" @selected($filters['disease'] === $slug)>
                            {{ config("diseases.{$slug}.label", $slug) }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div>
            <label class="mb-1 block text-[11px] font-medium text-slate-500">Cari pengguna</label>
            <input
                type="search"
                name="q"
                value="{{ $filters['q'] }}"
                placeholder="Nama atau email..."
                class="w-full rounded-xl border border-brand-200 px-3 py-2.5 text-sm"
            >
        </div>
        @if ($showMonthlyDetail ?? false)
            <input type="hidden" name="period_from" value="{{ $periodFrom }}">
            <input type="hidden" name="period_to" value="{{ $periodTo }}">
        @endif
        <div class="flex gap-2">
            <button type="submit" class="flex-1 rounded-xl bg-brand-600 py-2.5 text-sm font-semibold text-white">Terapkan</button>
            @if ($filters['q'] || $filters['type'] || $filters['disease'] || ($showMonthlyDetail ?? false))
                <a href="{{ route('admin.monitoring.index', ($showMonthlyDetail ?? false) ? array_filter(['period_from' => $periodFrom, 'period_to' => $periodTo]) : []) }}" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600">Reset</a>
            @endif
        </div>
    </form>

    @include('admin.partials.monitoring-monthly-section')

    @if ($showMonthlyDetail ?? false)
        @include('admin.partials.monitoring-charts')
    @endif

    @if ($showMonthlyDetail ?? false)
    <div class="space-y-3">
        @forelse ($entries as $entry)
            @include('admin.partials.monitoring-list-card', ['entry' => $entry])
        @empty
            <div class="rounded-2xl border border-dashed border-slate-200 bg-white p-8 text-center">
                <p class="text-sm text-slate-500">Belum ada data monitoring.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $entries->links() }}</div>
    @endif
@endsection
