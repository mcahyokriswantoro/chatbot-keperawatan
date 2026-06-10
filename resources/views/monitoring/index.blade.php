@extends('layouts.mobile')

@section('content')
    <x-mobile.page-header title="Monitoring Self Management" />

    <x-mobile.alert />

    <form method="POST" action="{{ route('monitoring.store') }}" class="mb-8 space-y-5">
        @csrf

        <section class="rounded-2xl bg-white p-4 shadow-card border border-brand-100 space-y-3">
            <h2 class="text-sm font-bold text-slate-900">Keluhan</h2>
            <textarea
                name="complaints"
                rows="3"
                class="w-full rounded-xl border border-brand-200 px-3 py-2 text-sm"
                placeholder="Tuliskan keluhan yang Anda rasakan hari ini..."
            >{{ old('complaints') }}</textarea>
        </section>

        <section class="rounded-2xl bg-white p-4 shadow-card border border-brand-100 space-y-3">
            <h2 class="text-sm font-bold text-slate-900">Obat</h2>
            <div>
                <label class="text-xs font-medium text-slate-600">Nama Obat</label>
                <input
                    type="text"
                    name="medication_name"
                    value="{{ old('medication_name') }}"
                    class="mt-1 w-full rounded-xl border border-brand-200 px-3 py-2 text-sm"
                    placeholder="Contoh: Metformin 500 mg"
                >
            </div>
            <div>
                <label class="text-xs font-medium text-slate-600">Dosis</label>
                <input
                    type="text"
                    name="medication_dose"
                    value="{{ old('medication_dose') }}"
                    class="mt-1 w-full rounded-xl border border-brand-200 px-3 py-2 text-sm"
                    placeholder="Contoh: 1 tablet"
                >
            </div>
            <div>
                <label class="text-xs font-medium text-slate-600">Jadwal</label>
                <input
                    type="text"
                    name="medication_schedule"
                    value="{{ old('medication_schedule') }}"
                    class="mt-1 w-full rounded-xl border border-brand-200 px-3 py-2 text-sm"
                    placeholder="Contoh: Pagi dan malam setelah makan"
                >
            </div>
        </section>

        <section class="rounded-2xl bg-white p-4 shadow-card border border-brand-100 space-y-3">
            <h2 class="text-sm font-bold text-slate-900">Aktivitas &amp; Latihan</h2>
            <textarea
                name="activities"
                rows="3"
                class="w-full rounded-xl border border-brand-200 px-3 py-2 text-sm"
                placeholder="Contoh: Jalan pagi 30 menit, latihan pernapasan..."
            >{{ old('activities') }}</textarea>
        </section>

        <section class="rounded-2xl bg-white p-4 shadow-card border border-brand-100 space-y-3">
            <h2 class="text-sm font-bold text-slate-900">Diet</h2>
            <p class="text-xs text-slate-600">Apakah sesuai anjuran?</p>
            <div class="flex gap-4">
                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                    <input type="radio" name="diet_compliant" value="ya" @checked(old('diet_compliant') === 'ya') class="text-brand-600 focus:ring-brand-500">
                    Ya
                </label>
                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                    <input type="radio" name="diet_compliant" value="tidak" @checked(old('diet_compliant') === 'tidak') class="text-brand-600 focus:ring-brand-500">
                    Tidak
                </label>
            </div>
            <div>
                <label class="text-xs font-medium text-slate-600">Contoh</label>
                <textarea
                    name="diet_notes"
                    rows="2"
                    class="mt-1 w-full rounded-xl border border-brand-200 px-3 py-2 text-sm"
                    placeholder="Contoh: Nasi merah, sayur bayam, ikan kukus..."
                >{{ old('diet_notes') }}</textarea>
            </div>
        </section>

        <section class="rounded-2xl bg-white p-4 shadow-card border border-brand-100 space-y-3">
            <h2 class="text-sm font-bold text-slate-900">Monitoring</h2>
            <div class="grid grid-cols-2 gap-3">
                <div class="col-span-2">
                    <label class="text-xs font-medium text-slate-600">Tekanan Darah (mmHg)</label>
                    <div class="mt-1 flex gap-2">
                        <input type="number" name="systolic" value="{{ old('systolic') }}" class="w-full rounded-xl border border-brand-200 px-3 py-2 text-sm" placeholder="Sistolik">
                        <span class="self-center text-slate-400">/</span>
                        <input type="number" name="diastolic" value="{{ old('diastolic') }}" class="w-full rounded-xl border border-brand-200 px-3 py-2 text-sm" placeholder="Diastolik">
                    </div>
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-600">Nadi (bpm)</label>
                    <input type="number" name="heart_rate" value="{{ old('heart_rate') }}" class="mt-1 w-full rounded-xl border border-brand-200 px-3 py-2 text-sm" placeholder="x/menit">
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-600">Suhu (°C)</label>
                    <input type="number" step="0.1" name="temperature" value="{{ old('temperature') }}" class="mt-1 w-full rounded-xl border border-brand-200 px-3 py-2 text-sm" placeholder="36.5">
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-600">Respiratory Rate</label>
                    <input type="number" name="respiratory_rate" value="{{ old('respiratory_rate') }}" class="mt-1 w-full rounded-xl border border-brand-200 px-3 py-2 text-sm" placeholder="x/menit">
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-600">Gula Darah (mg/dL)</label>
                    <input type="number" step="0.1" name="blood_sugar" value="{{ old('blood_sugar') }}" class="mt-1 w-full rounded-xl border border-brand-200 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-600">Saturasi O₂ (%)</label>
                    <input type="number" name="oxygen_saturation" value="{{ old('oxygen_saturation') }}" class="mt-1 w-full rounded-xl border border-brand-200 px-3 py-2 text-sm" placeholder="98">
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-600">Berat Badan (kg)</label>
                    <input type="number" step="0.1" name="weight" value="{{ old('weight') }}" class="mt-1 w-full rounded-xl border border-brand-200 px-3 py-2 text-sm">
                </div>
            </div>
            <div>
                <label class="text-xs font-medium text-slate-600">Tanggal Pencatatan</label>
                <input type="date" name="recorded_at" value="{{ old('recorded_at', date('Y-m-d')) }}" required class="mt-1 w-full rounded-xl border border-brand-200 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="text-xs font-medium text-slate-600">Catatan Tambahan (opsional)</label>
                <textarea name="notes" rows="2" class="mt-1 w-full rounded-xl border border-brand-200 px-3 py-2 text-sm" placeholder="Informasi lain yang perlu dicatat...">{{ old('notes') }}</textarea>
            </div>
        </section>

        <button type="submit" class="w-full rounded-full bg-brand-600 py-2.5 text-sm font-semibold text-white shadow-soft">Simpan Data</button>
    </form>

    <h2 class="mb-3 text-sm font-bold text-slate-900">Riwayat Monitoring</h2>
    @forelse ($records as $record)
        <article class="mb-3 rounded-2xl bg-white p-4 shadow-card border border-brand-100 space-y-3">
            <div class="flex items-center justify-between gap-2">
                <p class="font-semibold text-slate-900">{{ $record->recorded_at->format('d M Y') }}</p>
                @if ($record->dietCompliantLabel())
                    <span class="rounded-full bg-brand-50 px-2 py-0.5 text-[10px] font-semibold text-brand-700">
                        Diet: {{ $record->dietCompliantLabel() }}
                    </span>
                @endif
            </div>

            @if ($record->complaints)
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Keluhan</p>
                    <p class="mt-0.5 text-sm text-slate-700">{{ $record->complaints }}</p>
                </div>
            @endif

            @if ($record->medication_name || $record->medication_dose || $record->medication_schedule)
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Obat</p>
                    <dl class="mt-1 space-y-0.5 text-sm text-slate-700">
                        @if ($record->medication_name)
                            <div><span class="text-slate-500">Nama:</span> {{ $record->medication_name }}</div>
                        @endif
                        @if ($record->medication_dose)
                            <div><span class="text-slate-500">Dosis:</span> {{ $record->medication_dose }}</div>
                        @endif
                        @if ($record->medication_schedule)
                            <div><span class="text-slate-500">Jadwal:</span> {{ $record->medication_schedule }}</div>
                        @endif
                    </dl>
                </div>
            @endif

            @if ($record->activities)
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Aktivitas &amp; Latihan</p>
                    <p class="mt-0.5 text-sm text-slate-700">{{ $record->activities }}</p>
                </div>
            @endif

            @if ($record->diet_notes)
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Contoh Diet</p>
                    <p class="mt-0.5 text-sm text-slate-700">{{ $record->diet_notes }}</p>
                </div>
            @endif

            <div>
                <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Monitoring</p>
                <div class="mt-1 flex flex-wrap gap-2 text-xs text-slate-600">
                    @if ($record->bloodPressureLabel())
                        <span class="rounded-full bg-brand-50 px-2 py-1">TD: {{ $record->bloodPressureLabel() }}</span>
                    @endif
                    @if ($record->heart_rate)
                        <span class="rounded-full bg-brand-50 px-2 py-1">Nadi: {{ $record->heart_rate }} bpm</span>
                    @endif
                    @if ($record->temperature)
                        <span class="rounded-full bg-brand-50 px-2 py-1">Suhu: {{ $record->temperature }}°C</span>
                    @endif
                    @if ($record->respiratory_rate)
                        <span class="rounded-full bg-brand-50 px-2 py-1">RR: {{ $record->respiratory_rate }}/menit</span>
                    @endif
                    @if ($record->blood_sugar)
                        <span class="rounded-full bg-brand-50 px-2 py-1">GDS: {{ $record->blood_sugar }} mg/dL</span>
                    @endif
                    @if ($record->oxygen_saturation)
                        <span class="rounded-full bg-brand-50 px-2 py-1">SpO₂: {{ $record->oxygen_saturation }}%</span>
                    @endif
                    @if ($record->weight)
                        <span class="rounded-full bg-brand-50 px-2 py-1">BB: {{ $record->weight }} kg</span>
                    @endif
                </div>
            </div>

            @if ($record->notes)
                <p class="text-xs text-slate-500">{{ $record->notes }}</p>
            @endif
        </article>
    @empty
        <p class="text-sm text-slate-500 text-center py-4">Belum ada data monitoring.</p>
    @endforelse
    <div class="mt-4">{{ $records->links() }}</div>
@endsection
