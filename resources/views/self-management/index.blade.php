@extends('layouts.mobile')

@section('content')
    <x-mobile.page-header title="Self Management" />

    <x-mobile.alert />

    <section class="mb-6">
        <h2 class="mb-3 text-sm font-bold text-slate-900">Tambah Aktivitas</h2>
        <form method="POST" action="{{ route('self-management.store') }}" class="space-y-3 rounded-2xl bg-white p-4 shadow-card border border-brand-100">
            @csrf
            <div>
                <label class="text-xs font-medium text-slate-600">Jenis aktivitas</label>
                <select name="activity_type" class="mt-1 w-full rounded-xl border border-brand-200 px-3 py-2 text-sm" required>
                    @foreach ($activities as $act)
                        <option value="{{ $act['type'] }}">{{ $act['icon'] }} {{ $act['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-medium text-slate-600">Judul</label>
                <input type="text" name="title" required class="mt-1 w-full rounded-xl border border-brand-200 px-3 py-2 text-sm" placeholder="Contoh: Minum obat pagi">
            </div>
            <div>
                <label class="text-xs font-medium text-slate-600">Jadwal (opsional)</label>
                <input type="date" name="scheduled_for" class="mt-1 w-full rounded-xl border border-brand-200 px-3 py-2 text-sm">
            </div>
            <button type="submit" class="w-full rounded-full bg-brand-600 py-2.5 text-sm font-semibold text-white">Simpan</button>
        </form>
    </section>

    <section>
        <h2 class="mb-3 text-sm font-bold text-slate-900">Daftar Aktivitas</h2>
        @forelse ($logs as $log)
            <div class="mb-3 flex items-center gap-3 rounded-2xl bg-white p-4 shadow-card border border-brand-100">
                <form method="POST" action="{{ route('self-management.toggle', $log) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" @class([
                        'flex h-8 w-8 items-center justify-center rounded-full border-2',
                        'border-emerald-500 bg-emerald-500 text-white' => $log->completed,
                        'border-brand-300 text-transparent' => ! $log->completed,
                    ])>
                        ✓
                    </button>
                </form>
                <div class="min-w-0 flex-1">
                    <p @class(['font-semibold text-slate-900', 'line-through text-slate-400' => $log->completed])>{{ $log->title }}</p>
                    <p class="text-xs text-slate-500">{{ $log->activity_type }} @if($log->scheduled_for) · {{ $log->scheduled_for->format('d M Y') }}@endif</p>
                </div>
            </div>
        @empty
            <p class="text-sm text-slate-500 text-center py-6">Belum ada aktivitas. Tambahkan di atas.</p>
        @endforelse
        <div class="mt-4">{{ $logs->links() }}</div>
    </section>
@endsection
