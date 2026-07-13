@extends('layouts.admin')

@section('title', 'Skrining')

@section('content')
    <x-admin.page-banner title="Hasil Skrining" subtitle="Deteksi dini & penilaian risiko" tone="emerald" />

    <form method="GET" class="mb-4 space-y-3 rounded-2xl border border-brand-100 bg-white p-4 shadow-sm">
        <div>
            <label class="mb-1 block text-[11px] font-medium text-slate-500">Penyakit</label>
            <select name="disease" class="w-full rounded-xl border border-brand-200 bg-white px-3 py-2.5 text-sm">
                <option value="">Semua penyakit</option>
                @foreach ($diseases as $d)
                    <option value="{{ $d }}" @selected($filters['disease'] === $d)>{{ $stats->diseaseLabel($d) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-[11px] font-medium text-slate-500">Tingkat risiko</label>
            <select name="risk" class="w-full rounded-xl border border-brand-200 bg-white px-3 py-2.5 text-sm">
                <option value="">Semua risiko</option>
                @foreach (['low' => 'Rendah', 'medium' => 'Sedang', 'high' => 'Tinggi', 'emergency' => 'Darurat'] as $val => $label)
                    <option value="{{ $val }}" @selected($filters['risk'] === $val)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-[11px] font-medium text-slate-500">Cari nama</label>
            <input type="search" name="q" value="{{ $filters['q'] }}" placeholder="Nama subjek atau pengguna..." class="w-full rounded-xl border border-brand-200 px-3 py-2.5 text-sm">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="flex-1 rounded-xl bg-brand-600 py-2.5 text-sm font-semibold text-white">Terapkan</button>
            @if ($filters['disease'] || $filters['risk'] || $filters['q'])
                <a href="{{ route('admin.screenings.index') }}" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600">Reset</a>
            @endif
        </div>
    </form>

    @include('admin.partials.screening-charts')

    <div class="space-y-3">
        @forelse ($screenings as $s)
            @include('admin.partials.screening-list-card', [
                'session' => $s,
                'detailUrl' => route('admin.screenings.show', $s),
            ])
        @empty
            <div class="rounded-2xl border border-dashed border-slate-200 bg-white p-8 text-center">
                <p class="text-sm text-slate-500">Tidak ada data skrining.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $screenings->links() }}</div>
@endsection
