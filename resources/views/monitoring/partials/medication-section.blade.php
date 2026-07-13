@props([
    'userMedications',
    'inputClass',
])

@php
    $savedMedications = $userMedications
        ->map(fn ($med) => [
            'id' => $med->id,
            'name' => $med->name,
            'dose' => $med->dose,
            'schedule' => $med->schedule,
            'prescription_days' => $med->prescription_days,
            'purpose' => $med->purpose,
            'doctor_name' => $med->doctor_name,
            'on_time' => null,
            'notes' => null,
        ])
        ->values()
        ->all();

    $medicationRows = old('medications')
        ? collect(old('medications'))->values()->all()
        : ($savedMedications !== [] ? $savedMedications : [[
            'id' => null,
            'name' => '',
            'dose' => '',
            'schedule' => '',
            'prescription_days' => null,
            'purpose' => '',
            'doctor_name' => '',
            'on_time' => null,
            'notes' => null,
        ]]);

    $medicationStarted = filled(old('medications'));
    $baseMedicationIndex = count($medicationRows);
@endphp

<x-monitoring.section
    letter="b"
    title="Obat dari dokter"
    subtitle="Catat kepatuhan minum obat resep dokter Anda hari ini."
    accent="violet"
    class="!shadow-card"
>
    <div
        x-data="{
            started: @js($medicationStarted),
            medicationReady: false,
            baseIndex: @js($baseMedicationIndex),
            extraMeds: [],
            syncMedicationReady() {
                if (! this.started) {
                    this.medicationReady = false;
                    this.$dispatch('monitoring-daily-section', { section: 'medication', complete: false });
                    return;
                }

                const cards = this.$root.querySelectorAll('[data-medication-card]');
                let ready = cards.length > 0;
                cards.forEach((card) => {
                    if (! card.querySelector('input[type=radio]:checked')) {
                        ready = false;
                    }
                });
                this.medicationReady = ready;
                this.$dispatch('monitoring-daily-section', { section: 'medication', complete: ready });
            },
            addMedication() {
                this.extraMeds.push({ key: Date.now() });
                this.$nextTick(() => this.syncMedicationReady());
            },
        }"
        x-init="syncMedicationReady()"
        @change="syncMedicationReady()"
    >
        <div x-show="!started" x-cloak>
            <button
                type="button"
                @click="started = true; $nextTick(() => syncMedicationReady())"
                class="monitoring-btn-primary w-full rounded-2xl py-3 text-sm font-bold"
            >
                Mulai catat obat
            </button>
        </div>

        <template x-if="started">
            <div class="space-y-4">
                @foreach ($medicationRows as $index => $med)
                    @include('monitoring.partials.medication-card', [
                        'index' => $index,
                        'med' => $med,
                        'inputClass' => $inputClass,
                    ])
                @endforeach

                <template x-for="(med, offset) in extraMeds" :key="med.key">
                    <div data-medication-card class="space-y-3 rounded-2xl border border-violet-100 bg-violet-50/40 p-4 ring-1 ring-violet-100">
                        <p class="text-xs font-bold text-violet-900">💊 Obat baru</p>

                        <div>
                            <label class="text-xs font-semibold text-slate-700">Nama obat</label>
                            <input type="text" x-bind:name="'medications[' + (baseIndex + offset) + '][name]'" required class="{{ $inputClass }}" placeholder="Contoh: Metformin 500 mg">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-xs font-semibold text-slate-700">Dosis</label>
                                <input type="text" x-bind:name="'medications[' + (baseIndex + offset) + '][dose]'" class="{{ $inputClass }}" placeholder="1 tablet">
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-slate-700">Jadwal minum</label>
                                <input type="text" x-bind:name="'medications[' + (baseIndex + offset) + '][schedule]'" class="{{ $inputClass }}" placeholder="Pagi & malam">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-xs font-semibold text-slate-700">Berapa lama dokter memberikan resep? (hari)</label>
                                <input type="number" min="1" max="365" required x-bind:name="'medications[' + (baseIndex + offset) + '][prescription_days]'" class="{{ $inputClass }}" placeholder="Misalnya: 30">
                                <p class="mt-1 text-[10px] text-slate-500">Digunakan menghitung kepatuhan: hari tepat waktu ÷ durasi resep.</p>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-slate-700">Nama dokter</label>
                                <input type="text" x-bind:name="'medications[' + (baseIndex + offset) + '][doctor_name]'" class="{{ $inputClass }}" placeholder="Opsional">
                            </div>
                        </div>

                        <div>
                            <label class="text-xs font-semibold text-slate-700">Untuk apa obat ini?</label>
                            <input type="text" x-bind:name="'medications[' + (baseIndex + offset) + '][purpose]'" class="{{ $inputClass }}" placeholder="Contoh: kontrol gula darah">
                        </div>

                        <div class="mt-3">
                            <p class="monitoring-medication-question">Hari ini obat ini sudah diminum tepat waktu?</p>
                            <div class="monitoring-choice-grid monitoring-choice-grid--2">
                                <label class="monitoring-choice cursor-pointer">
                                    <input type="radio" x-bind:name="'medications[' + (baseIndex + offset) + '][on_time]'" value="ya" class="sr-only" required>
                                    <span class="monitoring-choice-pill">Ya, sudah</span>
                                </label>
                                <label class="monitoring-choice cursor-pointer">
                                    <input type="radio" x-bind:name="'medications[' + (baseIndex + offset) + '][on_time]'" value="tidak" class="sr-only">
                                    <span class="monitoring-choice-pill">Belum / terlewat</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="text-xs font-semibold text-slate-700">Catatan hari ini (opsional)</label>
                            <textarea x-bind:name="'medications[' + (baseIndex + offset) + '][notes]'" rows="2" class="{{ $inputClass }}" placeholder="Misalnya: telat 1 jam karena aktivitas"></textarea>
                        </div>
                    </div>
                </template>

                <button
                    type="button"
                    @click="addMedication()"
                    class="flex w-full items-center justify-center gap-2 rounded-2xl border border-dashed border-violet-200 bg-white py-3 text-sm font-semibold text-violet-700 transition hover:bg-violet-50"
                >
                    <span class="text-lg leading-none">+</span>
                    Tambah obat lain
                </button>

                <x-monitoring.section-save section="medication" label="Simpan data obat" ready="medicationReady" />
            </div>
        </template>
    </div>
</x-monitoring.section>
