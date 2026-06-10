@props(['session'])

@php
    $rows = $session->answerBreakdown();
    $positiveRows = array_values(array_filter($rows, fn ($row) => $row['is_positive']));
    $score = $session->scoreData();
    $progress = $session->scoreProgressPercent();
    $legend = $session->scoringLegend();
    $theme = $session->riskTheme();
@endphp

<section
    x-data="{
        filter: @js(count($positiveRows) > 0 ? 'positive' : 'all'),
        rows: @js($rows),
        matches(row) {
            if (this.filter === 'positive') return row.is_positive;
            if (this.filter === 'no') return ! row.is_positive;
            return true;
        },
        visibleRows() {
            return this.rows.filter(row => this.matches(row));
        },
        countPositive() {
            return this.rows.filter(row => row.is_positive).length;
        },
        countNo() {
            return this.rows.filter(row => ! row.is_positive).length;
        },
    }"
    class="rounded-2xl border border-brand-100 bg-white shadow-sm"
>
    <div class="border-b border-slate-100 px-4 py-4">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h2 class="text-sm font-bold text-slate-900">Jawaban Skrining</h2>
                <p class="mt-0.5 text-[11px] text-slate-500">Fokus pada temuan — skor per pertanyaan disembunyikan agar lebih mudah dibaca</p>
            </div>
            @if ($rows !== [])
                <span class="shrink-0 rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-semibold text-slate-600">
                    {{ count($rows) }} pertanyaan
                </span>
            @endif
        </div>

        @if ($progress !== null)
            <div class="mt-4">
                <div class="mb-1.5 flex items-center justify-between text-[11px]">
                    <span class="font-medium text-slate-500">Tingkat skor keseluruhan</span>
                    <span @class(['font-bold', $theme['text']])>{{ $score['total'] }}/{{ $score['max'] }}</span>
                </div>
                <div class="h-2.5 overflow-hidden rounded-full bg-slate-100">
                    <div
                        @class(['h-full rounded-full transition-all duration-500', $theme['accent']])
                        style="width: {{ $progress }}%"
                    ></div>
                </div>
                @if ($legend)
                    <p class="mt-2 text-[10px] leading-relaxed text-slate-400">{{ $legend }}</p>
                @endif
            </div>
        @endif
    </div>

    @if ($rows === [])
        <div class="px-4 py-6">
            <pre class="whitespace-pre-wrap font-sans text-xs leading-relaxed text-slate-600">{{ $session->summary }}</pre>
        </div>
    @else
        {{-- Filter tabs --}}
        <div class="flex gap-1.5 overflow-x-auto px-4 py-3 scrollbar-none">
            <button
                type="button"
                @click="filter = 'positive'"
                :class="filter === 'positive' ? 'bg-brand-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600'"
                class="shrink-0 rounded-full px-3 py-1.5 text-[11px] font-semibold transition"
            >
                Temuan (<span x-text="countPositive()"></span>)
            </button>
            <button
                type="button"
                @click="filter = 'all'"
                :class="filter === 'all' ? 'bg-brand-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600'"
                class="shrink-0 rounded-full px-3 py-1.5 text-[11px] font-semibold transition"
            >
                Semua (<span x-text="rows.length"></span>)
            </button>
            <button
                type="button"
                @click="filter = 'no'"
                :class="filter === 'no' ? 'bg-brand-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600'"
                class="shrink-0 rounded-full px-3 py-1.5 text-[11px] font-semibold transition"
            >
                Tidak (<span x-text="countNo()"></span>)
            </button>
        </div>

        {{-- Empty state per filter --}}
        <div x-show="visibleRows().length === 0" x-cloak class="px-4 pb-4">
            <div class="rounded-xl bg-emerald-50 px-4 py-5 text-center ring-1 ring-emerald-100">
                <p class="text-sm font-semibold text-emerald-800">Tidak ada temuan pada filter ini</p>
                <p class="mt-1 text-xs text-emerald-700">Semua pertanyaan dijawab Tidak — risiko relatif rendah.</p>
            </div>
        </div>

        {{-- Answer list --}}
        <div class="space-y-2 px-4 pb-4" x-show="visibleRows().length > 0">
            <template x-for="row in visibleRows()" :key="row.no">
                <div
                    class="rounded-xl border px-3 py-3 transition"
                    :class="row.is_positive
                        ? 'border-amber-200 bg-amber-50/70'
                        : 'border-slate-100 bg-slate-50/80'"
                >
                    <div class="flex items-start gap-3">
                        <span
                            class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg text-[11px] font-bold"
                            :class="row.is_positive ? 'bg-amber-200 text-amber-900' : 'bg-slate-200 text-slate-600'"
                            x-text="row.no"
                        ></span>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs leading-relaxed text-slate-800" x-text="row.text"></p>
                            <div class="mt-2 flex items-center gap-2">
                                <span
                                    class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wide"
                                    :class="row.is_positive
                                        ? 'bg-amber-200 text-amber-900'
                                        : 'bg-slate-200 text-slate-600'"
                                    x-text="row.answer_label"
                                ></span>
                                <span
                                    x-show="row.is_positive"
                                    x-cloak
                                    class="text-[10px] font-medium text-amber-700"
                                >Perlu perhatian</span>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        @if (count($positiveRows) > 0)
            <div class="border-t border-slate-100 px-4 py-3">
                <p class="text-[11px] leading-relaxed text-slate-500">
                    <span class="font-semibold text-slate-700">{{ count($positiveRows) }} temuan</span>
                    berkontribusi pada skor akhir. Lanjutkan dengan panduan self management sesuai tingkat risiko.
                </p>
            </div>
        @endif
    @endif
</section>

@push('scripts')
<style>
    .scrollbar-none::-webkit-scrollbar { display: none; }
    .scrollbar-none { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endpush
