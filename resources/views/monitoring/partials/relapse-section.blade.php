@props([
    'relapseOptions',
    'savedRelapseFrequency' => null,
])

<x-monitoring.section letter="b" title="Apakah gejala pernah kambuh?" :subtitle="\App\Support\MonitoringCopy::relapseSectionSubtitle()" accent="amber">
    <div x-show="!relapseStarted" x-cloak>
        <button
            type="button"
            @click="relapseStarted = true"
            class="monitoring-btn-primary w-full rounded-2xl py-3 text-sm font-bold"
        >
            Mulai catat kekambuhan
        </button>
    </div>

    <div x-show="relapseStarted" x-cloak class="space-y-3">
        @foreach ($relapseOptions as $option)
            <label class="monitoring-choice monitoring-choice--row group flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 bg-white px-3 py-3 text-slate-800 shadow-sm transition">
                <input
                    type="radio"
                    name="relapse_frequency"
                    value="{{ $option['value'] }}"
                    x-model="relapseFrequency"
                    @if(old('relapse_frequency', $savedRelapseFrequency) === $option['value']) checked @endif
                    @change="updateRelapse($event.target.value)"
                    class="h-4 w-4 shrink-0 border-slate-300 text-brand-600 focus:ring-brand-500"
                >
                <span class="flex-1 text-sm font-medium">{{ $option['label'] }}</span>
                <span class="relapse-score rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-500">{{ $option['score'] }} poin</span>
            </label>
        @endforeach

        <div x-show="relapseScoreLabel" x-cloak class="rounded-xl bg-amber-50 px-3 py-2.5 ring-1 ring-amber-100">
            <p class="text-[10px] font-semibold uppercase tracking-wide text-amber-800">Jawaban Anda</p>
            <p class="mt-1 text-xs text-slate-700">
                <span x-text="relapseChoiceLabel"></span>
                · skor <span x-text="relapseScore" class="font-bold"></span>
                · <span x-text="labelText(relapseScoreLabel)" class="font-semibold text-amber-900"></span>
            </p>
            <p class="mt-1 text-[10px] text-amber-700">Data ini otomatis masuk ke Ringkasan bulan ini di bawah.</p>
        </div>

        <button
            type="button"
            @click="relapseStarted = false"
            class="w-full rounded-xl border border-slate-200 bg-white py-2.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-50"
        >
            Tutup
        </button>

        <x-monitoring.section-save section="relapse" label="Simpan frekuensi kekambuhan" ready="relapseScoreLabel" />
    </div>
</x-monitoring.section>
