@props([
    'inputClass',
])

<x-monitoring.section letter="d" title="Tanda-tanda vital" subtitle="Masukkan pengukuran tanda vital jika ada" accent="emerald">
    <div x-show="!vitalsStarted" x-cloak>
        <button
            type="button"
            @click="vitalsStarted = true"
            class="monitoring-btn-primary w-full rounded-2xl py-3 text-sm font-bold"
        >
            Masukkan pengukuran tanda vital
        </button>
    </div>

    <div x-show="vitalsStarted" x-cloak>
        <p
            x-show="preview.vitals_summary"
            x-text="preview.vitals_summary"
            class="mb-3 rounded-xl bg-emerald-50 px-3 py-2.5 text-xs leading-relaxed text-emerald-900 ring-1 ring-emerald-100"
        ></p>
        <div class="grid grid-cols-2 gap-3">
            <div class="col-span-2">
                <label class="text-xs font-semibold text-slate-700">Tekanan Darah</label>
                <div class="mt-1.5 flex gap-2">
                    <input type="number" name="systolic" value="{{ old('systolic') }}" class="{{ $inputClass }} mt-0" placeholder="Sistolik">
                    <span class="self-center text-slate-400">/</span>
                    <input type="number" name="diastolic" value="{{ old('diastolic') }}" class="{{ $inputClass }} mt-0" placeholder="Diastolik">
                </div>
            </div>
            @foreach ([
                ['name' => 'heart_rate', 'label' => 'Nadi'],
                ['name' => 'temperature', 'label' => 'Suhu', 'step' => '0.1'],
                ['name' => 'respiratory_rate', 'label' => 'Resp. Rate'],
                ['name' => 'blood_sugar', 'label' => 'Gula Darah', 'step' => '0.1'],
                ['name' => 'oxygen_saturation', 'label' => 'SpO₂'],
                ['name' => 'weight', 'label' => 'Berat Badan', 'step' => '0.1'],
            ] as $field)
                <div>
                    <label class="text-xs font-semibold text-slate-700">{{ $field['label'] }}</label>
                    <input type="number" step="{{ $field['step'] ?? '1' }}" name="{{ $field['name'] }}" value="{{ old($field['name']) }}" class="{{ $inputClass }}">
                </div>
            @endforeach
        </div>

        <x-monitoring.section-save section="vitals" label="Simpan tanda vital" ready="vitalsStarted" />
    </div>
</x-monitoring.section>
