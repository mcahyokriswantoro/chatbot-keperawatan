@extends('layouts.mobile')

@section('content')
@php
    $categoryLabel = collect(config('consultation.categories', []))->firstWhere('key', $providerKey)['label'] ?? ($provider['specialty'] ?? 'Tenaga Kesehatan');
    $photoUrl = $provider['photo'] ?? asset('images/avatars/male.svg');
    $providerName = $provider['short_name'] ?? $provider['name'];
    $showInlinePay = $openPayModal || $errors->has('voucher_code') || old('voucher_code');
@endphp

<div
    x-data="{ showPay: {{ $showInlinePay ? 'true' : 'false' }} }"
    class="space-y-4 pb-28"
>
    {{-- Header --}}
    <header class="relative -mx-4 overflow-hidden bg-gradient-to-br from-emerald-500 via-brand-600 to-teal-600 px-5 pb-5 pt-2 text-white shadow-lg sm:mx-0 sm:rounded-3xl">
        <div class="pointer-events-none absolute -right-6 -top-6 h-28 w-28 rounded-full bg-white/10 blur-2xl"></div>
        <div class="relative flex items-center gap-2">
            <a
                href="{{ route('consultation.category', $categoryKey) }}"
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white/20 transition hover:bg-white/30"
                aria-label="Kembali"
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
            </a>
            <div class="min-w-0 flex-1">
                <p class="text-[10px] font-semibold uppercase tracking-wider text-emerald-100">Checkout konsultasi</p>
                <h1 class="truncate text-lg font-bold leading-tight">{{ $categoryLabel }}</h1>
            </div>
            @if ($accessState === 'active')
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-white/20 text-xl backdrop-blur-sm">✓</span>
            @elseif ($accessState === 'pending_verification')
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-white/20 text-xl backdrop-blur-sm">⏳</span>
            @else
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-white/20 text-xl backdrop-blur-sm">💬</span>
            @endif
        </div>
        <p class="relative mt-3 text-xs leading-relaxed text-white/90">
            @if ($accessState === 'active')
                Pembayaran terverifikasi — siap melakukan chat konsultasi dengan {{ $providerName }}.
            @elseif ($accessState === 'pending_verification')
                Bukti transfer sedang diverifikasi admin sebelum chat aktif.
            @elseif ($accessState === 'rejected')
                Pembayaran ditolak — bayar ulang untuk mengaktifkan chat.
            @else
                Bayar atau pakai voucher untuk mulai konsultasi online.
            @endif
        </p>
    </header>

    @if (session('upload_error'))
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-xs text-rose-800">
            {{ session('upload_error') }}
        </div>
    @endif

    {{-- Status chip --}}
    @if ($accessState === 'active')
        <div class="flex gap-2 overflow-x-auto pb-0.5">
            <span class="shrink-0 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-[10px] font-semibold text-emerald-700">✓ Terverifikasi</span>
            <span class="shrink-0 rounded-full border border-brand-200 bg-brand-50 px-3 py-1 text-[10px] font-semibold text-brand-700">Chat Konsultasi</span>
            <span class="shrink-0 rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-[10px] font-semibold text-slate-600">Sesi {{ $sessionHours }} jam</span>
        </div>
    @elseif ($accessState === 'pending_verification')
        <div class="flex items-start gap-3 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3.5">
            <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-amber-100 text-lg">⏳</span>
            <div class="min-w-0">
                <p class="text-sm font-bold text-amber-900">Menunggu verifikasi admin</p>
                <p class="mt-0.5 text-xs leading-relaxed text-amber-800">Bukti transfer sedang dicek. Chat akan aktif setelah admin menyetujui pembayaran.</p>
                <a href="{{ $statusUrl }}" class="mt-2 inline-flex text-xs font-bold text-amber-900 underline underline-offset-2">Lihat status pembayaran →</a>
            </div>
        </div>
    @elseif ($accessState === 'rejected')
        <div class="flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3.5">
            <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-rose-100 text-rose-700">✕</span>
            <div class="min-w-0">
                <p class="text-sm font-bold text-rose-900">Pembayaran ditolak</p>
                <p class="mt-0.5 text-xs leading-relaxed text-rose-800">Transfer tidak ditemukan atau nominal tidak sesuai. Silakan bayar ulang.</p>
            </div>
        </div>
    @else
        <div class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5">
            <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-200 text-slate-600">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
            </span>
            <div class="min-w-0">
                <p class="text-sm font-bold text-slate-900">Belum bisa chat</p>
                <p class="mt-0.5 text-xs leading-relaxed text-slate-600">Bayar dulu atau gunakan voucher 100% untuk mengaktifkan sesi chat.</p>
            </div>
        </div>
    @endif

    {{-- Kartu perawat --}}
    @if ($accessState === 'active')
        <a
            href="{{ $chatUrl }}"
            class="block overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm hover:border-brand-300 hover:shadow-md transition duration-200 active:scale-[0.99]"
        >
    @else
        <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    @endif
        <div class="flex">
            <div class="w-[110px] shrink-0 border-r border-slate-100 bg-slate-50 p-2">
                <div class="aspect-[3/4] overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-slate-100">
                    <img src="{{ $photoUrl }}" alt="{{ $providerName }}" class="h-full w-full object-cover object-top">
                </div>
            </div>
            <div class="flex min-w-0 flex-1 flex-col justify-between p-3">
                <div>
                    <div class="flex items-start justify-between gap-2">
                        <h2 class="text-sm font-bold leading-snug text-slate-900">{{ $providerName }}</h2>
                        @if ($accessState === 'active')
                            <span class="shrink-0 rounded-full bg-emerald-50 px-2 py-0.5 text-[9px] font-bold text-emerald-700">Siap chat</span>
                        @endif
                    </div>
                    <p class="mt-0.5 text-xs text-slate-500">{{ $provider['specialty'] ?? $provider['title'] }}</p>
                    <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-[11px] text-slate-600">
                        @if (! empty($provider['experience_years']))
                            <span class="inline-flex items-center gap-1">
                                <svg class="h-3.5 w-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.084-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                {{ $provider['experience_years'] }} tahun
                            </span>
                        @endif
                        @if (! empty($provider['rating_percent']))
                            <span class="inline-flex items-center gap-1">
                                <svg class="h-3.5 w-3.5 text-emerald-500" viewBox="0 0 24 24" fill="currentColor"><path d="M7.493 18.75c-.425 0-.82-.236-1.025-.605a1.09 1.09 0 01-.014-1.134l3.053-5.27A1.01 1.01 0 0110.5 11.5h3a1.01 1.01 0 01.893.241l3.053 5.27a1.09 1.09 0 01-.014 1.134 1.012 1.012 0 01-1.025.605H7.493z"/></svg>
                                {{ $provider['rating_percent'] }}%
                            </span>
                        @endif
                    </div>
                </div>
                <div class="mt-3">
                    <p class="text-[10px] text-slate-400">Biaya konsultasi</p>
                    <p class="text-base font-bold text-slate-900">{{ $priceLabel }}</p>
                </div>
            </div>
        </div>
    @if ($accessState === 'active')
        </a>
    @else
        </article>
    @endif



    {{-- Langkah alur (belum aktif) --}}
    @if ($accessState !== 'active')
        <div class="grid grid-cols-3 gap-2">
            <div @class([
                'rounded-xl border px-2 py-2.5 text-center',
                'border-emerald-300 bg-emerald-50 ring-2 ring-emerald-200' => in_array($accessState, ['awaiting_payment', 'rejected'], true),
                'border-slate-200 bg-white' => $accessState === 'pending_verification',
            ])>
                <p class="text-[10px] font-bold text-emerald-700">1</p>
                <p class="mt-0.5 text-[9px] font-semibold leading-tight text-slate-700">Bayar / voucher</p>
            </div>
            <div @class([
                'rounded-xl border px-2 py-2.5 text-center',
                'border-amber-300 bg-amber-50 ring-2 ring-amber-200' => $accessState === 'pending_verification',
                'border-slate-200 bg-white' => $accessState !== 'pending_verification',
            ])>
                <p class="text-[10px] font-bold text-amber-700">2</p>
                <p class="mt-0.5 text-[9px] font-semibold leading-tight text-slate-700">Verifikasi admin</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white px-2 py-2.5 text-center opacity-60">
                <p class="text-[10px] font-bold text-slate-500">3</p>
                <p class="mt-0.5 text-[9px] font-semibold leading-tight text-slate-700">Chat Konsultasi</p>
            </div>
        </div>
    @endif

    {{-- Inline bayar (mulai=1) --}}
    @if (in_array($accessState, ['awaiting_payment', 'rejected'], true))
        <section
            x-show="showPay"
            x-cloak
            class="space-y-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm"
        >
            <div class="flex items-center justify-between gap-2">
                <div>
                    <p class="text-sm font-bold text-slate-900">Aktifkan chat</p>
                    <p class="text-xs text-slate-500">{{ $providerName }} · {{ $priceLabel }}</p>
                </div>
                <button type="button" @click="showPay = false" class="text-xs font-semibold text-slate-400 hover:text-slate-600">Tutup</button>
            </div>

            <section class="rounded-2xl border border-violet-100 bg-violet-50/50 p-4">
                <h3 class="text-sm font-bold text-slate-900">Voucher diskon</h3>
                <p class="mb-3 mt-1 text-xs text-slate-600">Kode voucher 100%, 50%, atau 25%.</p>
                <form method="POST" action="{{ route('consultation.voucher', $providerKey) }}" class="space-y-3">
                    @csrf
                    <input
                        type="text"
                        name="voucher_code"
                        value="{{ old('voucher_code') }}"
                        placeholder="PERAWAT100"
                        class="w-full rounded-xl border border-brand-200 bg-white px-3 py-2.5 text-sm uppercase placeholder:normal-case focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-100"
                    >
                    @error('voucher_code')
                        <p class="text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                    <button type="submit" class="w-full rounded-full bg-violet-600 py-3 text-sm font-semibold text-white hover:bg-violet-700">
                        Pakai voucher
                    </button>
                </form>
            </section>

            <div class="relative">
                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-200"></div></div>
                <div class="relative flex justify-center text-xs"><span class="bg-white px-2 text-slate-400">atau</span></div>
            </div>

            <section class="rounded-2xl border border-[#00529c]/20 bg-[#00529c]/5 p-4">
                <h3 class="text-sm font-bold text-slate-900">Bayar via Transfer Bank (Giro BRI)</h3>
                <p class="mb-3 mt-1 text-xs text-slate-600">Transfer + upload bukti transfer, lalu tunggu verifikasi admin.</p>
                <a
                    href="{{ route('consultation.payment', $providerKey) }}"
                    class="flex w-full items-center justify-center gap-2 rounded-full bg-[#00529c] py-3.5 text-sm font-bold text-white shadow-sm transition hover:bg-[#004787] active:scale-[0.98]"
                >
                    Bayar {{ $priceLabel }}
                </a>
            </section>
        </section>
    @endif

    {{-- Sticky CTA bawah --}}
    <div class="fixed inset-x-0 bottom-[4.5rem] z-40 border-t border-slate-200 bg-white/95 px-4 py-3 shadow-[0_-4px_20px_rgba(0,0,0,0.06)] backdrop-blur-sm sm:bottom-0">
        @if ($accessState === 'active')
            <a
                href="{{ $chatUrl }}"
                class="flex w-full items-center justify-center gap-2.5 rounded-2xl bg-brand-600 py-4 text-sm font-bold text-white shadow-lg shadow-brand-600/25 transition hover:bg-brand-700 active:scale-[0.98]"
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                </svg>
                Mulai Chat Konsultasi
            </a>
        @elseif ($accessState === 'pending_verification')
            <a
                href="{{ $statusUrl }}"
                class="flex w-full items-center justify-center gap-2 rounded-2xl bg-amber-500 py-4 text-sm font-bold text-white shadow-sm transition hover:bg-amber-600 active:scale-[0.98]"
            >
                ⏳ Menunggu verifikasi admin
            </a>
        @elseif ($accessState === 'rejected')
            <button
                type="button"
                @click="showPay = true; window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' })"
                class="flex w-full items-center justify-center gap-2 rounded-2xl bg-rose-500 py-4 text-sm font-bold text-white shadow-sm transition hover:bg-rose-600 active:scale-[0.98]"
            >
                Bayar ulang · {{ $priceLabel }}
            </button>
        @else
            <button
                type="button"
                @click="showPay = true; window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' })"
                class="flex w-full items-center justify-center gap-2 rounded-2xl bg-rose-500 py-4 text-sm font-bold text-white shadow-sm transition hover:bg-rose-600 active:scale-[0.98]"
            >
                Bayar dulu · {{ $priceLabel }}
            </button>
        @endif
    </div>
</div>

@if ($accessState === 'active' && request()->boolean('mulai'))
    @push('scripts')
    <script>
        document.getElementById('whatsapp-ready')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    </script>
    @endpush
@endif
@endsection
