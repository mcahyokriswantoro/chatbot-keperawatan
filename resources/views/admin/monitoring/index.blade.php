@extends('layouts.admin')

@section('title', 'Monitoring')

@section('content')
    <x-admin.page-banner title="Monitoring Kesehatan" :subtitle="$totalEntries.' entri tercatat'" tone="violet" />

    <form method="GET" class="mb-4 flex gap-2">
        <input
            type="search"
            name="q"
            value="{{ $search }}"
            placeholder="Cari nama pengguna..."
            class="min-w-0 flex-1 rounded-xl border border-brand-200 bg-white px-3 py-2.5 text-sm"
        >
        <button type="submit" class="shrink-0 rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white">Cari</button>
    </form>

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
@endsection
