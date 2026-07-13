@props([
    'index',
    'inputClass',
    'name' => '',
    'dose' => '',
    'schedule' => '',
    'prescriptionDays' => '',
    'purpose' => '',
    'doctorName' => '',
    'requirePrescription' => false,
])

<div>
    <label class="text-xs font-semibold text-slate-700">Nama obat</label>
    <input
        type="text"
        name="medications[{{ $index }}][name]"
        value="{{ $name }}"
        required
        class="{{ $inputClass }}"
        placeholder="Contoh: Metformin 500 mg"
    >
</div>

<div class="grid grid-cols-2 gap-3">
    <div>
        <label class="text-xs font-semibold text-slate-700">Dosis</label>
        <input
            type="text"
            name="medications[{{ $index }}][dose]"
            value="{{ $dose }}"
            class="{{ $inputClass }}"
            placeholder="1 tablet"
        >
    </div>
    <div>
        <label class="text-xs font-semibold text-slate-700">Jadwal minum</label>
        <input
            type="text"
            name="medications[{{ $index }}][schedule]"
            value="{{ $schedule }}"
            class="{{ $inputClass }}"
            placeholder="Pagi & malam"
        >
    </div>
</div>

<div class="grid grid-cols-2 gap-3">
    <div>
        <label class="text-xs font-semibold text-slate-700">Berapa lama dokter memberikan resep? (hari)</label>
        <input
            type="number"
            name="medications[{{ $index }}][prescription_days]"
            value="{{ $prescriptionDays }}"
            min="1"
            max="365"
            @if ($requirePrescription) required @endif
            class="{{ $inputClass }}"
            placeholder="Misalnya: 30"
        >
        <p class="mt-1 text-[10px] text-slate-500">Digunakan menghitung kepatuhan: hari tepat waktu ÷ durasi resep.</p>
    </div>
    <div>
        <label class="text-xs font-semibold text-slate-700">Nama dokter</label>
        <input
            type="text"
            name="medications[{{ $index }}][doctor_name]"
            value="{{ $doctorName }}"
            class="{{ $inputClass }}"
            placeholder="Opsional"
        >
    </div>
</div>

<div>
    <label class="text-xs font-semibold text-slate-700">Untuk apa obat ini?</label>
    <input
        type="text"
        name="medications[{{ $index }}][purpose]"
        value="{{ $purpose }}"
        class="{{ $inputClass }}"
        placeholder="Contoh: kontrol gula darah"
    >
</div>
