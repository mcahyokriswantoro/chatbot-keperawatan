@extends('layouts.mobile')

@section('content')
<div
    x-data="{
        search: '',
        categories: @js($categories),
        filtered() {
            if (! this.search.trim()) return this.categories;
            const q = this.search.toLowerCase();
            return this.categories.filter(c =>
                c.label.toLowerCase().includes(q)
                || (c.description || '').toLowerCase().includes(q)
            );
        },
        primary() {
            return this.filtered().filter(c => c.active);
        },
        categoryUrl(key) {
            return `/konsultasi/${key}`;
        },
    }"
    class="space-y-5 pb-2"
>
    <header class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-500 via-brand-600 to-teal-600 px-5 py-6 text-white shadow-lg">
        <div class="pointer-events-none absolute -right-8 -top-8 h-32 w-32 rounded-full bg-white/10 blur-2xl"></div>
        <div class="pointer-events-none absolute -bottom-6 -left-6 h-24 w-24 rounded-full bg-white/10 blur-xl"></div>
        <p class="text-[10px] font-semibold uppercase tracking-wider text-emerald-100">Nersia Health</p>
        <h1 class="mt-1 text-xl font-bold leading-tight">Chat dengan Tenaga Kesehatan</h1>
        <p class="mt-2 text-xs leading-relaxed text-white/90">
            Konsultasi chat berbayar per sesi. Gunakan voucher 100% untuk gratis, atau bayar sebelum chat.
        </p>
    </header>

    <div class="relative">
        <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
        <input
            type="search"
            x-model="search"
            placeholder="Cari tenaga kesehatan atau spesialisasi..."
            class="w-full rounded-2xl border border-brand-100 bg-white py-3 pl-10 pr-4 text-sm shadow-sm placeholder:text-slate-400 focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-100"
        />
    </div>

    <section>
        <h2 class="mb-3 text-sm font-bold text-slate-900">Pilih tenaga kesehatan</h2>
        <div class="space-y-3">
            <template x-for="cat in primary()" :key="cat.key">
                <a
                    :href="categoryUrl(cat.key)"
                    class="flex w-full items-center gap-4 rounded-2xl border border-emerald-200 bg-white p-4 text-left shadow-card transition hover:border-emerald-300 active:scale-[0.99]"
                >
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-2xl" x-text="cat.icon"></span>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="font-bold text-slate-900" x-text="cat.label"></h3>
                            <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold text-emerald-700">Berbayar · Chat aktif</span>
                        </div>
                        <p class="mt-0.5 text-xs leading-relaxed text-slate-500" x-text="cat.description"></p>
                    </div>
                </a>
            </template>
            <p x-show="primary().length === 0" x-cloak class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-6 text-center text-xs text-slate-500">
                Belum ada layanan chat aktif.
            </p>
        </div>
    </section>

    @if (count($otherDoctors) > 0)
    <section>
        <h2 class="mb-3 text-sm font-bold text-slate-900">Spesialisasi lainnya</h2>
        <div class="grid grid-cols-2 gap-3">
            @foreach ($otherDoctors as $cat)
                <div class="flex flex-col rounded-2xl border border-brand-100 bg-white p-4 shadow-sm opacity-95">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-50 text-xl">{{ $cat['icon'] ?? '👨‍⚕️' }}</span>
                    <p class="mt-2 text-xs font-bold leading-snug text-slate-900">{{ $cat['label'] }}</p>
                    <p class="mt-1 line-clamp-2 text-[10px] leading-snug text-slate-500">{{ $cat['description'] ?? '' }}</p>
                    <span class="mt-2 inline-flex w-fit rounded-full bg-slate-100 px-2 py-0.5 text-[9px] font-semibold text-slate-500">Segera hadir</span>
                </div>
            @endforeach
        </div>
    </section>
    @endif

    <div x-show="filtered().length === 0" x-cloak class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-8 text-center">
        <p class="text-sm text-slate-500">Tenaga kesehatan tidak ditemukan</p>
    </div>

    <p class="text-center text-[11px] leading-relaxed text-slate-400">
        Konsultasi chat berbayar per sesi. Voucher 100% = gratis. Bukan pengganti pemeriksaan medis. Darurat: <a href="{{ route('emergency') }}" class="font-semibold text-brand-600">hotline</a>.
    </p>
</div>
@endsection
