@extends('layouts.mobile')

@section('content')
@php
    $categoryIcon = $category['icon'] ?? '🩺';
@endphp

<div
    x-data="{
        search: '',
        subcategories: @js($subcategories),
        filtered() {
            if (! this.search.trim()) return this.subcategories;
            const q = this.search.toLowerCase();
            return this.subcategories.filter(c =>
                c.label.toLowerCase().includes(q)
                || (c.description || '').toLowerCase().includes(q)
            );
        },
    }"
    class="space-y-4 pb-4"
>
    <header class="relative -mx-4 overflow-hidden bg-gradient-to-br from-violet-500 via-brand-600 to-indigo-600 px-5 pb-5 pt-2 text-white shadow-lg sm:mx-0 sm:rounded-3xl">
        <div class="pointer-events-none absolute -right-6 -top-6 h-28 w-28 rounded-full bg-white/10 blur-2xl"></div>
        <div class="relative flex items-center gap-2">
            <a
                href="{{ route('consultation.index') }}"
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white/20 transition hover:bg-white/30"
                aria-label="Kembali"
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
            </a>
            <div class="min-w-0 flex-1">
                <p class="text-[10px] font-semibold uppercase tracking-wider text-violet-100">Dokter Spesialis</p>
                <h1 class="truncate text-lg font-bold leading-tight">{{ $category['label'] }}</h1>
            </div>
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-white/20 text-2xl backdrop-blur-sm">{{ $categoryIcon }}</span>
        </div>
        <p class="relative mt-3 text-xs leading-relaxed text-white/90">
            {{ $category['description'] ?? 'Pilih bidang spesialisasi dokter.' }}
        </p>
    </header>

    <div class="relative">
        <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
        <input
            type="search"
            x-model="search"
            placeholder="Cari spesialisasi..."
            class="w-full rounded-2xl border border-brand-100 bg-white py-3 pl-10 pr-4 text-sm shadow-sm placeholder:text-slate-400 focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-100"
        />
    </div>

    <section>
        <h2 class="mb-3 text-sm font-bold text-slate-900">Pilih spesialisasi</h2>
        <div class="grid grid-cols-2 gap-3">
            <template x-for="cat in filtered()" :key="cat.key">
                <div class="flex flex-col rounded-2xl border border-brand-100 bg-white p-4 shadow-sm opacity-95">
                    <span class="text-2xl" x-text="cat.icon"></span>
                    <p class="mt-2 text-xs font-bold leading-snug text-slate-900" x-text="cat.label"></p>
                    <p class="mt-1 line-clamp-2 text-[10px] leading-snug text-slate-500" x-text="cat.description"></p>
                    <span class="mt-2 inline-flex w-fit rounded-full bg-slate-100 px-2 py-0.5 text-[9px] font-semibold text-slate-500">Segera hadir</span>
                </div>
            </template>
        </div>

        <div x-show="filtered().length === 0" x-cloak class="mt-3 rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-10 text-center">
            <p class="text-sm text-slate-500">Spesialisasi tidak ditemukan</p>
        </div>
    </section>

    <p class="text-center text-[10px] leading-relaxed text-slate-400">
        Semua layanan dokter spesialis akan segera hadir. Saat ini chat aktif untuk Perawat.
    </p>
</div>
@endsection
