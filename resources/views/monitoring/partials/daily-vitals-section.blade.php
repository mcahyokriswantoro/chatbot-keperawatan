@props([
    'inputClass',
])

@php
    $vitalsStarted = filled(old('systolic'))
        || filled(old('diastolic'))
        || filled(old('heart_rate'))
        || filled(old('temperature'))
        || filled(old('respiratory_rate'))
        || filled(old('blood_sugar'))
        || filled(old('oxygen_saturation'))
        || filled(old('weight'));
@endphp

<x-monitoring.section letter="c" title="Tanda-tanda vital" subtitle="Masukkan pengukuran tanda vital jika ada" accent="emerald">
    <div x-data="{ started: @js($vitalsStarted) }">
        <div x-show="!started" x-cloak>
            <button
                type="button"
                @click="started = true"
                class="monitoring-btn-primary w-full rounded-2xl py-3 text-sm font-bold"
            >
                Masukkan pengukuran tanda vital
            </button>
        </div>

        <template x-if="started">
            <div class="space-y-3">
                <div class="grid grid-cols-2 gap-3">
                    <div class="col-span-2 rounded-xl bg-emerald-50/50 p-3 ring-1 ring-emerald-100">
                        <label class="text-xs font-semibold text-slate-700">Tekanan Darah (mmHg)</label>
                        <div class="mt-1.5 flex items-center gap-2">
                            <input type="number" name="systolic" value="{{ old('systolic') }}" class="{{ $inputClass }} mt-0" placeholder="120">
                            <span class="text-slate-400">/</span>
                            <input type="number" name="diastolic" value="{{ old('diastolic') }}" class="{{ $inputClass }} mt-0" placeholder="80">
                        </div>
                    </div>
                    @foreach ([
                        ['name' => 'heart_rate', 'label' => 'Nadi (bpm)', 'placeholder' => '72'],
                        ['name' => 'temperature', 'label' => 'Suhu (°C)', 'placeholder' => '36.5', 'step' => '0.1'],
                        ['name' => 'respiratory_rate', 'label' => 'Resp. Rate', 'placeholder' => '18'],
                        ['name' => 'blood_sugar', 'label' => 'Gula Darah', 'placeholder' => '100', 'step' => '0.1'],
                        ['name' => 'oxygen_saturation', 'label' => 'SpO₂ (%)', 'placeholder' => '98'],
                        ['name' => 'weight', 'label' => 'Berat (kg)', 'placeholder' => '65', 'step' => '0.1'],
                    ] as $field)
                        <div>
                            <label class="text-xs font-semibold text-slate-700">{{ $field['label'] }}</label>
                            <input
                                type="number"
                                step="{{ $field['step'] ?? '1' }}"
                                name="{{ $field['name'] }}"
                                value="{{ old($field['name']) }}"
                                class="{{ $inputClass }}"
                                placeholder="{{ $field['placeholder'] }}"
                            >
                        </div>
                    @endforeach
                </div>
                <button
                    type="button"
                    @click="started = false"
                    class="w-full rounded-xl border border-slate-200 bg-white py-2.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-50"
                >
                    Tutup
                </button>
                <x-monitoring.section-save section="vitals" label="Simpan tanda vital" ready="started" />
            </div>
        </template>
    </div>
</x-monitoring.section>

<style>[x-cloak]{display:none!important}</style>
