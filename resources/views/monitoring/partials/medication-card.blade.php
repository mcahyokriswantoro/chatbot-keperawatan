@props([
    'index',
    'med',
    'inputClass',
])

@php
    $isSaved = ! empty($med['id']);
    $name = old("medications.{$index}.name", $med['name'] ?? '');
    $dose = old("medications.{$index}.dose", $med['dose'] ?? '');
    $schedule = old("medications.{$index}.schedule", $med['schedule'] ?? '');
    $prescriptionDays = old("medications.{$index}.prescription_days", $med['prescription_days'] ?? '');
    $purpose = old("medications.{$index}.purpose", $med['purpose'] ?? '');
    $doctorName = old("medications.{$index}.doctor_name", $med['doctor_name'] ?? '');
    $onTimeSelected = old("medications.{$index}.on_time", $med['on_time'] ?? null);
@endphp

<div
    data-medication-card
    @if ($isSaved) x-data="{ editProfile: false }" @endif
    class="space-y-3 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm ring-1 ring-slate-100"
>
    @if ($isSaved)
        <input type="hidden" name="medications[{{ $index }}][id]" value="{{ $med['id'] }}">

        <div class="flex items-start gap-3 rounded-xl bg-violet-50 px-3 py-3 ring-1 ring-violet-100">
            <span class="text-lg">💊</span>
            <div class="min-w-0 flex-1 text-xs text-violet-900">
                <p class="font-bold">{{ $name }}</p>
                @if ($dose || $schedule)
                    <p class="mt-1 text-violet-700">{{ collect([$dose, $schedule])->filter()->implode(' · ') }}</p>
                @endif
                @if ($prescriptionDays)
                    <p class="mt-0.5 text-[10px] text-violet-600">Resep {{ $prescriptionDays }} hari</p>
                @endif
            </div>
        </div>

        <div x-show="editProfile" x-cloak class="space-y-3">
            @include('monitoring.partials.medication-profile-fields', [
                'index' => $index,
                'inputClass' => $inputClass,
                'name' => $name,
                'dose' => $dose,
                'schedule' => $schedule,
                'prescriptionDays' => $prescriptionDays,
                'purpose' => $purpose,
                'doctorName' => $doctorName,
            ])
        </div>

        <div x-show="!editProfile">
            <input type="hidden" name="medications[{{ $index }}][name]" value="{{ $name }}">
            <input type="hidden" name="medications[{{ $index }}][dose]" value="{{ $dose }}">
            <input type="hidden" name="medications[{{ $index }}][schedule]" value="{{ $schedule }}">
            <input type="hidden" name="medications[{{ $index }}][prescription_days]" value="{{ $prescriptionDays }}">
            <input type="hidden" name="medications[{{ $index }}][purpose]" value="{{ $purpose }}">
            <input type="hidden" name="medications[{{ $index }}][doctor_name]" value="{{ $doctorName }}">
            <button type="button" @click="editProfile = true" class="text-[11px] font-semibold text-violet-700 underline-offset-2 hover:underline">
                Ubah profil obat
            </button>
        </div>
    @else
        <p class="text-xs font-bold text-slate-900">💊 Obat {{ $index + 1 }}</p>
        @include('monitoring.partials.medication-profile-fields', [
            'index' => $index,
            'inputClass' => $inputClass,
            'name' => $name,
            'dose' => $dose,
            'schedule' => $schedule,
            'prescriptionDays' => $prescriptionDays,
            'purpose' => $purpose,
            'doctorName' => $doctorName,
            'requirePrescription' => true,
        ])
    @endif

    <div class="mt-3">
        <p class="monitoring-medication-question">Hari ini obat ini sudah diminum tepat waktu?</p>
        <x-monitoring.choice-grid
                name="medications[{{ $index }}][on_time]"
                :options="[['value' => 'ya', 'label' => 'Ya, sudah'], ['value' => 'tidak', 'label' => 'Belum / terlewat']]"
                :columns="2"
                :selected="$onTimeSelected"
            />
    </div>

    <div class="mt-3">
        <label class="text-xs font-semibold text-slate-700">Catatan hari ini (opsional)</label>
        <textarea
            name="medications[{{ $index }}][notes]"
            rows="2"
            class="{{ $inputClass }}"
            placeholder="Misalnya: telat 1 jam karena aktivitas"
        >{{ old("medications.{$index}.notes", $med['notes'] ?? '') }}</textarea>
    </div>
</div>
