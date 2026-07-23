@extends('layouts.mobile')

@section('title', 'Toko Obat & Vitamin')

@section('content')
<div class="space-y-4">
    {{-- Header --}}
    <header class="relative -mx-4 overflow-hidden bg-gradient-to-br from-[#00529c] via-[#004787] to-[#003366] px-5 py-4 text-white shadow-md sm:mx-0 sm:rounded-3xl">
        <div class="pointer-events-none absolute -right-8 -top-8 h-32 w-32 rounded-full bg-white/10 blur-2xl"></div>
        <div class="relative flex items-center gap-3">
            <a href="{{ route('home') }}" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white/20 transition hover:bg-white/30" aria-label="Kembali">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
            </a>
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-wider text-blue-100">Layanan Apotek Online</p>
                <h1 class="text-base font-bold">Obat & Vitamin</h1>
            </div>
        </div>
    </header>

    {{-- Status Flash Alert --}}
    @if (session('status'))
        <div class="rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-xs font-semibold text-emerald-800 shadow-sm flex items-center gap-2">
            <span>✨</span>
            <span class="flex-1">{{ session('status') }}</span>
        </div>
    @endif
    @if (session('error'))
        <div class="rounded-xl border border-rose-100 bg-rose-50 px-4 py-3 text-xs font-semibold text-rose-800 shadow-sm flex items-center gap-2">
            <span>⚠️</span>
            <span class="flex-1">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Search Bar --}}
    <form method="GET" action="{{ route('medicines.index') }}" class="relative">
        @if ($currentCategory)
            <input type="hidden" name="category" value="{{ $currentCategory }}">
        @endif
        <div class="relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.602 10.602z"/></svg>
            </span>
            <input
                type="text"
                name="q"
                value="{{ $search }}"
                placeholder="Cari obat, vitamin, atau suplemen..."
                class="w-full rounded-2xl border border-slate-200 bg-white pl-10 pr-4 py-3 text-sm focus:border-[#00529c] focus:outline-none focus:ring-2 focus:ring-[#00529c]/15 shadow-sm"
            >
        </div>
    </form>

    {{-- Categories --}}
    <div class="flex gap-1.5 overflow-x-auto pb-1.5 -mx-4 px-4 sm:mx-0 sm:px-0">
        <a
            href="{{ route('medicines.index', array_filter(['q' => $search])) }}"
            @class([
                'shrink-0 rounded-full px-4 py-2 text-xs font-bold transition shadow-sm',
                'bg-[#00529c] text-white' => !$currentCategory,
                'border border-slate-200 bg-white text-slate-600 hover:bg-slate-50' => $currentCategory,
            ])
        >
            Semua
        </a>
        @foreach ($categories as $cat)
            <a
                href="{{ route('medicines.index', array_filter(['category' => $cat, 'q' => $search])) }}"
                @class([
                    'shrink-0 rounded-full px-4 py-2 text-xs font-bold transition shadow-sm',
                    'bg-[#00529c] text-white' => $currentCategory === $cat,
                    'border border-slate-200 bg-white text-slate-600 hover:bg-slate-50' => $currentCategory !== $cat,
                ])
            >
                {{ $cat }}
            </a>
        @endforeach
    </div>

    {{-- Products Grid --}}
    <div class="grid grid-cols-2 gap-3">
        @forelse ($medicines as $med)
            <section class="flex flex-col rounded-2xl border border-slate-100 bg-white p-3 shadow-sm hover:shadow-md transition">
                <div class="relative mb-3 flex h-28 items-center justify-center overflow-hidden rounded-xl bg-slate-50">
                    <img
                        src="{{ $med->photoUrl() }}"
                        alt="{{ $med->name }}"
                        class="max-h-24 max-w-full object-contain object-center transition duration-300 hover:scale-105"
                    >
                    @if ($med->stock <= 0)
                        <div class="absolute inset-0 flex items-center justify-center bg-slate-900/40 backdrop-blur-[1px]">
                            <span class="rounded-full bg-rose-600 px-2.5 py-1 text-[9px] font-bold text-white uppercase tracking-wider">Habis</span>
                        </div>
                    @endif
                </div>

                <div class="flex-1 flex flex-col min-w-0">
                    <div class="mb-1">
                        <span @class([
                            'rounded-md px-1.5 py-0.5 text-[8px] font-bold uppercase tracking-wider',
                            'bg-emerald-50 text-emerald-700' => $med->category === 'Obat Bebas',
                            'bg-violet-50 text-violet-700' => $med->category === 'Vitamin & Suplemen',
                            'bg-rose-50 text-rose-700' => $med->category === 'Obat Keras',
                            'bg-slate-100 text-slate-700' => !in_array($med->category, ['Obat Bebas', 'Vitamin & Suplemen', 'Obat Keras']),
                        ])>
                            {{ $med->category }}
                        </span>
                    </div>

                    <h3 class="line-clamp-2 text-xs font-bold text-slate-800 leading-snug" title="{{ $med->name }}">
                        {{ $med->name }}
                    </h3>

                    <p class="mt-1 line-clamp-2 text-[10px] leading-relaxed text-slate-500 flex-1">
                        {{ $med->description }}
                    </p>

                    <div class="mt-3 flex items-center justify-between gap-1.5 pt-2 border-t border-slate-50">
                        <div>
                            <p class="text-[9px] text-slate-400 font-medium">Harga</p>
                            <p class="text-xs font-extrabold text-[#00529c]">
                                Rp {{ number_format($med->price, 0, ',', '.') }}
                            </p>
                        </div>

                        @if ($med->stock > 0)
                            <form method="POST" action="{{ route('medicines.cart.add') }}">
                                @csrf
                                <input type="hidden" name="medicine_id" value="{{ $med->id }}">
                                <input type="hidden" name="quantity" value="1">
                                <button
                                    type="submit"
                                    class="flex h-8 w-8 items-center justify-center rounded-xl bg-[#00529c]/10 text-[#00529c] transition hover:bg-[#00529c] hover:text-white"
                                    title="Tambah ke keranjang"
                                >
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                </button>
                            </form>
                        @else
                            <button
                                type="button"
                                disabled
                                class="flex h-8 w-8 items-center justify-center rounded-xl bg-slate-100 text-slate-400 cursor-not-allowed"
                            >
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                            </button>
                        @endif
                    </div>
                </div>
            </section>
        @empty
            <div class="col-span-2 rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-12 text-center text-xs text-slate-500">
                Tidak ada produk obat atau vitamin ditemukan.
            </div>
        @endforelse
    </div>
</div>

{{-- Sticky Floating Cart --}}
@if ($cartCount > 0)
    <div class="fixed inset-x-0 px-4 py-2" style="bottom: 78px; z-index: 60;">
        <a
            href="{{ route('medicines.cart') }}"
            class="mx-auto flex max-w-md items-center justify-between gap-3 rounded-2xl bg-[#00529c] px-4 py-3.5 text-white shadow-lg shadow-[#00529c]/25 transition hover:bg-[#004787] active:scale-[0.98]"
        >
            <div class="flex items-center gap-2.5">
                <span class="relative flex h-8 w-8 items-center justify-center rounded-lg bg-white/20">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
                    <span class="absolute -right-2 -top-2 flex h-5 w-5 items-center justify-center rounded-full bg-rose-600 text-[10px] font-bold text-white ring-2 ring-[#00529c]">{{ $cartCount }}</span>
                </span>
                <span class="text-xs font-bold">Keranjang Obat Anda</span>
            </div>
            <div class="flex items-center gap-1">
                <span class="text-xs font-extrabold uppercase">Lihat Detail</span>
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
            </div>
        </a>
    </div>
@endif
@endsection
