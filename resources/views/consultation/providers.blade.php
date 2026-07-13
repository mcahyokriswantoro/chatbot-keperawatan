@extends('layouts.mobile')

@section('content')
@php
    $categoryIcon = $category['icon'] ?? '👩‍⚕️';
    $providerCount = count($providers);
@endphp

<div
    x-data="{
        search: '',
        providers: @js($providers),
        filtered() {
            if (! this.search.trim()) return this.providers;
            const q = this.search.toLowerCase();
            return this.providers.filter(p =>
                p.short_name.toLowerCase().includes(q)
                || (p.specialty || '').toLowerCase().includes(q)
                || (p.name || '').toLowerCase().includes(q)
            );
        },
    }"
    class="space-y-4 pb-4"
>
    {{-- Header kategori --}}
    <header class="relative -mx-4 overflow-hidden bg-gradient-to-br from-emerald-500 via-brand-600 to-teal-600 px-5 pb-5 pt-2 text-white shadow-lg sm:mx-0 sm:rounded-3xl">
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
                <p class="text-[10px] font-semibold uppercase tracking-wider text-emerald-100">Konsultasi online</p>
                <h1 class="truncate text-lg font-bold leading-tight">{{ $category['label'] }}</h1>
            </div>
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-white/20 text-2xl backdrop-blur-sm">{{ $categoryIcon }}</span>
        </div>
        <p class="relative mt-3 text-xs leading-relaxed text-white/90">
            {{ $category['description'] ?? 'Pilih tenaga kesehatan, lalu lanjut voucher atau pembayaran DANA sebelum memulai chat.' }}
        </p>
        <div class="relative mt-3 inline-flex items-center gap-1.5 rounded-full bg-white/20 px-3 py-1 text-[11px] font-semibold backdrop-blur-sm">
            <span class="h-1.5 w-1.5 rounded-full bg-emerald-300"></span>
            {{ $providerCount }} tenaga kesehatan tersedia
        </div>
    </header>

    {{-- Cari --}}
    <div class="relative">
        <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
        <input
            type="search"
            x-model="search"
            placeholder="Cari nama atau spesialisasi..."
            class="w-full rounded-2xl border border-brand-100 bg-white py-3 pl-10 pr-4 text-sm shadow-sm placeholder:text-slate-400 focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-100"
        />
    </div>

    {{-- Info singkat --}}
    <div class="flex gap-2 overflow-x-auto pb-0.5">
        <span class="shrink-0 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-[10px] font-semibold text-emerald-700">Chat berbayar</span>
        <span class="shrink-0 rounded-full border border-violet-200 bg-violet-50 px-3 py-1 text-[10px] font-semibold text-violet-700">Voucher diskon</span>
        <span class="shrink-0 rounded-full border border-sky-200 bg-sky-50 px-3 py-1 text-[10px] font-semibold text-sky-700">Bayar DANA</span>
        <span class="shrink-0 rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-[10px] font-semibold text-slate-600">Sesi {{ $sessionHours }} jam</span>
    </div>

    {{-- Daftar kartu --}}
    <section>
        <h2 class="mb-3 text-sm font-bold text-slate-900">Pilih tenaga kesehatan</h2>

        <div class="space-y-3">
            <template x-for="item in filtered()" :key="item.key">
                <a
                    :href="`/konsultasi/${item.key}/checkout?mulai=1`"
                    class="block overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-card transition hover:border-emerald-300 hover:shadow-md active:scale-[0.99]"
                >
                    <div class="flex">
                        <div class="w-[110px] shrink-0 border-r border-slate-100 bg-slate-50 p-2">
                            <div class="aspect-[3/4] overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-slate-100">
                                <img
                                    :src="item.photo"
                                    :alt="item.short_name"
                                    class="h-full w-full object-cover object-top"
                                    loading="lazy"
                                >
                            </div>
                        </div>
                        <div class="flex min-w-0 flex-1 flex-col justify-between p-3">
                            <div>
                                <h3 class="text-sm font-bold leading-snug text-slate-900" x-text="item.short_name"></h3>
                                <p class="mt-0.5 text-xs text-slate-500" x-text="item.specialty"></p>
                                <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-[11px] text-slate-600">
                                    <template x-if="item.experience_years">
                                        <span class="inline-flex items-center gap-1">
                                            <svg class="h-3.5 w-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.084-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                            <span x-text="item.experience_years + ' tahun'"></span>
                                        </span>
                                    </template>
                                    <template x-if="item.rating_percent">
                                        <span class="inline-flex items-center gap-1 text-emerald-600">
                                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M7.493 18.75c-.425 0-.82-.236-1.025-.605a1.09 1.09 0 01-.014-1.134l3.053-5.27A1.01 1.01 0 0110.5 11.5h3a1.01 1.01 0 01.893.241l3.053 5.27a1.09 1.09 0 01-.014 1.134 1.012 1.012 0 01-1.025.605H7.493z"/></svg>
                                            <span x-text="item.rating_percent + '%'"></span>
                                        </span>
                                    </template>
                                </div>
                            </div>
                            <div class="mt-3 flex items-end justify-between gap-2">
                                <div>
                                    <p class="text-base font-bold text-slate-900" x-text="item.price_label"></p>
                                    <p class="text-[10px] text-slate-400">/ sesi chat</p>
                                </div>
                                <span class="shrink-0 rounded-lg bg-rose-500 px-5 py-2 text-xs font-bold text-white shadow-sm">Pilih</span>
                            </div>
                        </div>
                    </div>
                </a>
            </template>
        </div>

        <div x-show="filtered().length === 0" x-cloak class="mt-3 rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-10 text-center">
            <p class="text-sm font-medium text-slate-600">Tenaga kesehatan tidak ditemukan</p>
            <p class="mt-1 text-xs text-slate-400">Coba kata kunci lain atau kosongkan pencarian.</p>
        </div>
    </section>

    <p class="text-center text-[10px] leading-relaxed text-slate-400">
        Pilih tenaga kesehatan → voucher atau bayar DANA → verifikasi admin → chat konsultasi.
    </p>
</div>
@endsection
