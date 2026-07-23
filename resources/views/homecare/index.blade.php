@extends('layouts.mobile')

@section('title', 'Layanan Homecare Perawat')

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
                <p class="text-[10px] font-semibold uppercase tracking-wider text-blue-100">Kunjungan Perawat ke Rumah</p>
                <h1 class="text-base font-bold">Layanan Homecare</h1>
            </div>
        </div>
    </header>

    {{-- Hero Promo banner --}}
    <div class="rounded-2xl bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100 p-4 flex gap-3 shadow-sm">
        <span class="text-3xl">🏠</span>
        <div class="min-w-0 flex-1">
            <h2 class="text-xs font-bold text-slate-800">Perawat Profesional di Rumah Anda</h2>
            <p class="mt-1 text-[11px] text-slate-500 leading-relaxed">
                Butuh perawatan medis rutin tapi malas keluar rumah? Booking perawat bersertifikat kami sekarang. Layanan medis steril dan aman.
            </p>
        </div>
    </div>

    {{-- Package List --}}
    <div class="space-y-3">
        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Pilih Paket Layanan</h2>
        @forelse ($packages as $pkg)
            <section class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm flex gap-3 hover:border-[#00529c]/30 hover:shadow-md transition">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#00529c]/5 text-2xl">
                    {{ $pkg->icon }}
                </span>
                <div class="min-w-0 flex-1 flex flex-col justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800">{{ $pkg->name }}</h3>
                        <p class="mt-1 text-xs text-slate-500 leading-relaxed">{{ $pkg->description }}</p>
                    </div>
                    <div class="mt-4 flex items-center justify-between border-t border-slate-50 pt-3">
                        <div>
                            <p class="text-[9px] text-slate-400 font-medium">Estimasi Biaya</p>
                            <p class="text-sm font-extrabold text-[#00529c]">
                                Rp {{ number_format($pkg->price, 0, ',', '.') }}
                            </p>
                        </div>
                        <a
                            href="{{ route('homecare.book', $pkg) }}"
                            class="rounded-full bg-[#00529c] px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-[#004787] active:scale-95 transition"
                        >
                            Booking Layanan
                        </a>
                    </div>
                </div>
            </section>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-12 text-center text-xs text-slate-500">
                Tidak ada paket layanan homecare tersedia saat ini.
            </div>
        @endforelse
    </div>
</div>
@endsection
