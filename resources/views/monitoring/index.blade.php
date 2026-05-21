@extends('layouts.mobile')

@section('content')
    <x-mobile.page-header title="Monitoring Kesehatan" />

    <x-mobile.alert />

    <form method="POST" action="{{ route('monitoring.store') }}" class="mb-6 space-y-3 rounded-2xl bg-white p-4 shadow-card border border-brand-100">
        @csrf
        <p class="text-sm font-bold text-slate-900">Catat Data Vital</p>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="text-xs text-slate-600">Sistolik</label>
                <input type="number" name="systolic" class="mt-1 w-full rounded-xl border border-brand-200 px-3 py-2 text-sm" placeholder="mmHg">
            </div>
            <div>
                <label class="text-xs text-slate-600">Diastolik</label>
                <input type="number" name="diastolic" class="mt-1 w-full rounded-xl border border-brand-200 px-3 py-2 text-sm" placeholder="mmHg">
            </div>
            <div>
                <label class="text-xs text-slate-600">Detak jantung</label>
                <input type="number" name="heart_rate" class="mt-1 w-full rounded-xl border border-brand-200 px-3 py-2 text-sm" placeholder="bpm">
            </div>
            <div>
                <label class="text-xs text-slate-600">Gula darah</label>
                <input type="number" step="0.1" name="blood_sugar" class="mt-1 w-full rounded-xl border border-brand-200 px-3 py-2 text-sm" placeholder="mg/dL">
            </div>
            <div class="col-span-2">
                <label class="text-xs text-slate-600">Berat badan (kg)</label>
                <input type="number" step="0.1" name="weight" class="mt-1 w-full rounded-xl border border-brand-200 px-3 py-2 text-sm">
            </div>
            <div class="col-span-2">
                <label class="text-xs text-slate-600">Tanggal</label>
                <input type="date" name="recorded_at" value="{{ date('Y-m-d') }}" required class="mt-1 w-full rounded-xl border border-brand-200 px-3 py-2 text-sm">
            </div>
        </div>
        <button type="submit" class="w-full rounded-full bg-brand-600 py-2.5 text-sm font-semibold text-white">Simpan Data</button>
    </form>

    <h2 class="mb-3 text-sm font-bold text-slate-900">Riwayat Monitoring</h2>
    @forelse ($records as $record)
        <div class="mb-3 rounded-2xl bg-white p-4 shadow-card border border-brand-100">
            <p class="font-semibold text-slate-900">{{ $record->recorded_at->format('d M Y') }}</p>
            <div class="mt-2 flex flex-wrap gap-2 text-xs text-slate-600">
                @if ($record->systolic && $record->diastolic)
                    <span class="rounded-full bg-brand-50 px-2 py-1">TD: {{ $record->systolic }}/{{ $record->diastolic }}</span>
                @endif
                @if ($record->heart_rate)
                    <span class="rounded-full bg-brand-50 px-2 py-1">HR: {{ $record->heart_rate }} bpm</span>
                @endif
                @if ($record->blood_sugar)
                    <span class="rounded-full bg-brand-50 px-2 py-1">GDS: {{ $record->blood_sugar }}</span>
                @endif
                @if ($record->weight)
                    <span class="rounded-full bg-brand-50 px-2 py-1">BB: {{ $record->weight }} kg</span>
                @endif
            </div>
        </div>
    @empty
        <p class="text-sm text-slate-500 text-center py-4">Belum ada data monitoring.</p>
    @endforelse
    <div class="mt-4">{{ $records->links() }}</div>
@endsection
