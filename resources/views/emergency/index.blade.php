@extends('layouts.mobile')

@section('content')
<div
    x-data="{
        category: 'all',
        search: '',
        selectedSymptoms: [],
        fastChecked: {},
        openWarning: null,
        callTarget: null,
        hotlines: @js($hotlines),
        categories: @js($categories),
        toggleSymptom(id) {
            if (this.selectedSymptoms.includes(id)) {
                this.selectedSymptoms = this.selectedSymptoms.filter(s => s !== id);
            } else {
                this.selectedSymptoms = [...this.selectedSymptoms, id];
            }
        },
        toggleFast(key) {
            this.fastChecked = { ...this.fastChecked, [key]: !this.fastChecked[key] };
        },
        fastCount() {
            return Object.values(this.fastChecked).filter(Boolean).length;
        },
        filteredHotlines() {
            return this.hotlines.filter(h => {
                const matchCat = this.category === 'all' || h.category === this.category;
                if (!matchCat) return false;
                if (!this.search.trim()) return true;
                const q = this.search.toLowerCase();
                return h.name.toLowerCase().includes(q)
                    || h.number.includes(q)
                    || h.description.toLowerCase().includes(q);
            });
        },
        confirmCall(hotline) {
            this.callTarget = hotline;
        },
        cancelCall() {
            this.callTarget = null;
        },
    }"
    @keydown.escape.window="callTarget = null"
    class="space-y-5 pb-2"
>
    {{-- Hero --}}
    <header class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-rose-600 via-red-600 to-rose-700 px-4 pb-5 pt-4 text-white shadow-lg">
        <div class="pointer-events-none absolute -right-8 -top-8 h-32 w-32 rounded-full bg-white/10 blur-xl"></div>
        <div class="relative flex items-start gap-3">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-white/20 text-2xl ring-2 ring-white/30 backdrop-blur-sm">🚨</div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-rose-100">Nersia Health</p>
                <h1 class="text-xl font-bold leading-tight">Peringatan Darurat</h1>
                <p class="mt-1 text-xs text-white/85">Bantu menilai situasi & hubungi bantuan dengan cepat</p>
            </div>
        </div>
        <button
            type="button"
            @click="confirmCall({ name: 'Ambulans / PSC 119', number: '119' })"
            class="relative mt-4 flex w-full items-center justify-center gap-2 rounded-2xl bg-white py-3.5 text-sm font-bold text-rose-700 shadow-lg transition active:scale-[0.98] hover:bg-rose-50"
        >
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>
            Hubungi 119 Sekarang
        </button>
    </header>

    {{-- Cek gejala interaktif --}}
    <section class="rounded-2xl border border-rose-100 bg-white p-4 shadow-sm">
        <div class="mb-3 flex items-center justify-between gap-2">
            <h2 class="text-sm font-bold text-slate-900">⚡ Cek Gejala Darurat</h2>
            <span
                x-show="selectedSymptoms.length > 0"
                x-cloak
                class="rounded-full bg-rose-100 px-2.5 py-0.5 text-[10px] font-bold text-rose-700"
                x-text="selectedSymptoms.length + ' terpilih'"
            ></span>
        </div>
        <p class="mb-3 text-xs text-slate-500">Tap gejala yang Anda atau pasien alami saat ini:</p>
        <div class="grid grid-cols-1 gap-2">
            @foreach ($symptoms as $symptom)
                <button
                    type="button"
                    @click="toggleSymptom(@js($symptom['id']))"
                    :class="selectedSymptoms.includes(@js($symptom['id']))
                        ? 'border-rose-400 bg-rose-50 ring-2 ring-rose-200'
                        : 'border-slate-100 bg-slate-50 hover:border-rose-200'"
                    class="flex items-center gap-3 rounded-xl border px-3 py-2.5 text-left transition active:scale-[0.99]"
                >
                    <span class="text-lg" aria-hidden="true">{{ $symptom['icon'] }}</span>
                    <span class="flex-1 text-xs font-medium leading-snug text-slate-800">{{ $symptom['label'] }}</span>
                    <span
                        class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full border-2 transition"
                        :class="selectedSymptoms.includes(@js($symptom['id'])) ? 'border-rose-600 bg-rose-600 text-white' : 'border-slate-300'"
                    >
                        <svg x-show="selectedSymptoms.includes(@js($symptom['id']))" x-cloak class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                    </span>
                </button>
            @endforeach
        </div>

        <div
            x-show="selectedSymptoms.length > 0 || fastCount() > 0"
            x-cloak
            x-transition
            class="mt-4 rounded-xl border border-rose-300 bg-rose-50 p-3"
        >
            <p class="text-xs font-bold text-rose-800">⚠️ Situasi berpotensi darurat</p>
            <p class="mt-1 text-[11px] leading-relaxed text-rose-700">Segera hubungi 119 atau ke IGD terdekat. Jangan menunggu skrining selesai.</p>
            <button
                type="button"
                @click="confirmCall({ name: 'Ambulans / PSC 119', number: '119' })"
                class="mt-3 w-full rounded-full bg-rose-600 py-2.5 text-xs font-bold text-white transition hover:bg-rose-700 active:scale-[0.98]"
            >
                Hubungi Ambulans 119
            </button>
        </div>
    </section>

    {{-- FAST Stroke --}}
    <section class="rounded-2xl border border-violet-100 bg-white p-4 shadow-sm">
        <h2 class="mb-1 text-sm font-bold text-slate-900">🧠 Skrining Cepat Stroke (FAST)</h2>
        <p class="mb-3 text-xs text-slate-500">Centang tanda yang terlihat — 1 saja sudah cukup untuk ke IGD:</p>
        <div class="space-y-2">
            @foreach ($fast as $item)
                <button
                    type="button"
                    @click="toggleFast(@js($item['key']))"
                    :class="fastChecked[@js($item['key'])]
                        ? 'border-violet-400 bg-violet-50'
                        : 'border-slate-100 bg-slate-50'"
                    class="w-full rounded-xl border p-3 text-left transition active:scale-[0.99]"
                >
                    <div class="flex items-start gap-3">
                        <span
                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-sm font-black"
                            :class="fastChecked[@js($item['key'])] ? 'bg-violet-600 text-white' : 'bg-violet-100 text-violet-700'"
                            x-text="@js($item['key'])"
                        ></span>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-bold text-slate-900">{{ $item['title'] }}</p>
                            <p class="mt-0.5 text-[11px] leading-relaxed text-slate-600">{{ $item['desc'] }}</p>
                        </div>
                    </div>
                </button>
            @endforeach
        </div>
    </section>

    {{-- Langkah cepat --}}
    <section>
        <h2 class="mb-3 text-sm font-bold text-slate-900">📍 Langkah Cepat</h2>
        <div class="space-y-0">
            @foreach ($steps as $index => $step)
                <div class="relative flex gap-3 pb-4 last:pb-0">
                    @if (! $loop->last)
                        <div class="absolute left-[18px] top-10 h-[calc(100%-1.5rem)] w-0.5 bg-brand-100"></div>
                    @endif
                    <span class="relative z-10 flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-50 text-base ring-4 ring-white">{{ $step['icon'] }}</span>
                    <div class="min-w-0 flex-1 rounded-xl border border-brand-50 bg-white px-3 py-2.5 shadow-sm">
                        <p class="text-xs font-bold text-slate-900">{{ $step['title'] }}</p>
                        <p class="mt-0.5 text-[11px] leading-relaxed text-slate-500">{{ $step['desc'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Hotline --}}
    <section>
        <h2 class="mb-3 text-sm font-bold text-slate-900">📞 Hotline Darurat</h2>

        <div class="relative mb-3">
            <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
            <input
                type="search"
                x-model="search"
                placeholder="Cari layanan atau nomor..."
                class="w-full rounded-xl border border-brand-100 bg-white py-2.5 pl-9 pr-3 text-sm focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-100"
            />
        </div>

        <div class="mb-3 flex gap-2 overflow-x-auto pb-1 scrollbar-none">
            <template x-for="cat in categories" :key="cat.id">
                <button
                    type="button"
                    @click="category = cat.id"
                    :class="category === cat.id
                        ? 'bg-rose-600 text-white shadow-sm'
                        : 'bg-white text-slate-600 ring-1 ring-slate-200'"
                    class="shrink-0 rounded-full px-3.5 py-2 text-[11px] font-semibold transition active:scale-95"
                >
                    <span x-text="cat.icon + ' ' + cat.label"></span>
                </button>
            </template>
        </div>

        <div class="space-y-2">
            <template x-for="hotline in filteredHotlines()" :key="hotline.number">
                <div class="flex items-center gap-2 rounded-2xl border border-rose-50 bg-white p-3 shadow-sm">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-rose-50 text-xl" x-text="hotline.icon"></span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-bold text-slate-900" x-text="hotline.name"></p>
                        <p class="truncate text-[11px] text-slate-500" x-text="hotline.description"></p>
                    </div>
                    <button
                        type="button"
                        @click="confirmCall(hotline)"
                        class="shrink-0 rounded-full bg-rose-600 px-3 py-2 text-xs font-bold text-white transition hover:bg-rose-700 active:scale-95"
                        x-text="hotline.number"
                    ></button>
                </div>
            </template>
            <p x-show="filteredHotlines().length === 0" x-cloak class="py-6 text-center text-xs text-slate-400">Tidak ada hotline ditemukan.</p>
        </div>
    </section>

    {{-- FAQ accordion --}}
    <section>
        <h2 class="mb-3 text-sm font-bold text-slate-900">ℹ️ Peringatan Penting</h2>
        <div class="space-y-2">
            @foreach ($warnings as $index => $warning)
                <div class="overflow-hidden rounded-2xl border border-amber-100 bg-white shadow-sm">
                    <button
                        type="button"
                        @click="openWarning = openWarning === {{ $index }} ? null : {{ $index }}"
                        class="flex w-full items-center justify-between gap-2 px-4 py-3 text-left"
                    >
                        <span class="text-xs font-bold text-amber-900">{{ $warning['title'] }}</span>
                        <svg
                            class="h-4 w-4 shrink-0 text-amber-600 transition"
                            :class="openWarning === {{ $index }} ? 'rotate-180' : ''"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                        ><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                    </button>
                    <div
                        x-show="openWarning === {{ $index }}"
                        x-cloak
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="border-t border-amber-50 px-4 pb-3 pt-2"
                    >
                        <p class="text-[11px] leading-relaxed text-amber-900/90">{{ $warning['body'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Tautan bantuan --}}
    <div class="grid grid-cols-2 gap-2">
        <a href="{{ route('help') }}" class="rounded-2xl border border-brand-100 bg-brand-50 px-3 py-3 text-center transition hover:bg-brand-100 active:scale-[0.98]">
            <span class="text-lg">💬</span>
            <p class="mt-1 text-[11px] font-bold text-brand-800">Bantuan App</p>
        </a>
        <a href="{{ auth()->check() ? route('detection.start') : route('login') }}" class="rounded-2xl border border-brand-100 bg-white px-3 py-3 text-center shadow-sm transition hover:shadow-md active:scale-[0.98]">
            <span class="text-lg">🔍</span>
            <p class="mt-1 text-[11px] font-bold text-slate-800">Deteksi Dini</p>
        </a>
    </div>

    {{-- Modal konfirmasi telepon --}}
    <div
        x-show="callTarget"
        x-cloak
        class="fixed inset-0 z-[60] flex items-end justify-center bg-black/50 p-4 backdrop-blur-sm sm:items-center"
        @click.self="cancelCall()"
    >
        <div
            x-show="callTarget"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            class="w-full max-w-sm overflow-hidden rounded-3xl bg-white shadow-2xl"
            @click.stop
        >
            <div class="bg-rose-600 px-5 py-4 text-white">
                <p class="text-xs font-medium text-rose-100">Konfirmasi panggilan</p>
                <p class="mt-1 text-lg font-bold" x-text="callTarget?.name"></p>
            </div>
            <div class="p-5">
                <p class="text-sm text-slate-600">
                    Anda akan menelepon
                    <span class="font-bold text-rose-600" x-text="callTarget?.number"></span>.
                    Pastikan situasi memang memerlukan bantuan darurat.
                </p>
                <div class="mt-5 flex gap-2">
                    <button type="button" @click="cancelCall()" class="flex-1 rounded-full border border-slate-200 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                        Batal
                    </button>
                    <a
                        :href="callTarget ? 'tel:' + callTarget.number : '#'"
                        @click="cancelCall()"
                        class="flex flex-1 items-center justify-center gap-2 rounded-full bg-rose-600 py-3 text-sm font-bold text-white transition hover:bg-rose-700 active:scale-[0.98]"
                    >
                        Telepon
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
    .scrollbar-none::-webkit-scrollbar { display: none; }
    .scrollbar-none { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endpush
