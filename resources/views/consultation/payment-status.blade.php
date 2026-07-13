@extends('layouts.mobile')

@section('content')
@php
    $photoUrl = $provider['photo'] ?? asset('images/avatars/male.svg');
    $providerName = $provider['short_name'] ?? $provider['name'];
@endphp

<div class="space-y-4 pb-28">
    <header class="relative -mx-4 overflow-hidden bg-gradient-to-br from-emerald-500 via-brand-600 to-teal-600 px-5 pb-5 pt-2 text-white shadow-lg sm:mx-0 sm:rounded-3xl">
        <div class="relative flex items-center gap-2">
            <a
                href="{{ route('consultation.checkout', $providerKey) }}"
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white/20 transition hover:bg-white/30"
                aria-label="Kembali"
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
            </a>
            <div class="min-w-0 flex-1">
                <p class="text-[10px] font-semibold uppercase tracking-wider text-emerald-100">Status pembayaran</p>
                <h1 class="truncate text-lg font-bold">{{ $providerName }}</h1>
            </div>
        </div>
    </header>

    @if (session('status'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('status') }}
        </div>
    @endif

    @if ($state === 'pending')
        <div class="overflow-hidden rounded-2xl border border-amber-200 bg-amber-50 p-5 text-center">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-amber-100 text-2xl">⏳</div>
            <h2 class="mt-3 text-base font-bold text-slate-900">Menunggu verifikasi admin</h2>
            <p class="mt-2 text-xs leading-relaxed text-slate-600">
                Bukti transfer sedang dicek. Halaman ini diperbarui otomatis setelah admin menyetujui.
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4 text-sm shadow-sm">
            <dl class="space-y-2.5">
                <div class="flex justify-between gap-3">
                    <dt class="text-slate-500">Nominal</dt>
                    <dd class="font-bold text-slate-900">{{ $priceLabel }}</dd>
                </div>
                @if ($order?->reference_code)
                    <div class="flex justify-between gap-3">
                        <dt class="text-slate-500">ID transaksi</dt>
                        <dd class="font-mono text-xs text-slate-700">{{ $order->reference_code }}</dd>
                    </div>
                @endif
                @if ($order?->dana_phone)
                    <div class="flex justify-between gap-3">
                        <dt class="text-slate-500">Identitas pengirim</dt>
                        <dd class="max-w-[60%] text-right text-xs font-medium leading-snug text-slate-900">{{ $order->dana_phone }}</dd>
                    </div>
                @endif
                @if ($order?->paymentProofUrl())
                    <div class="flex justify-between gap-3">
                        <dt class="text-slate-500">Bukti transfer</dt>
                        <dd class="text-right text-xs font-medium text-emerald-700">✓ Sudah terkirim</dd>
                    </div>
                @endif
            </dl>
        </div>

        <div id="poll-indicator" class="flex items-center justify-center gap-2 text-xs text-slate-400">
            <span class="inline-block h-2 w-2 animate-pulse rounded-full bg-amber-400"></span>
            Memeriksa status…
        </div>

        <div class="fixed inset-x-0 bottom-[4.5rem] z-40 border-t border-slate-200 bg-white/95 px-4 py-3 shadow-[0_-4px_20px_rgba(0,0,0,0.06)] backdrop-blur-sm sm:bottom-0">
            <a
                href="{{ route('consultation.checkout', $providerKey) }}"
                class="flex w-full items-center justify-center gap-2 rounded-2xl bg-amber-500 py-4 text-sm font-bold text-white shadow-sm transition hover:bg-amber-600 active:scale-[0.98]"
            >
                ⏳ Menunggu verifikasi admin
            </a>
        </div>

    @elseif ($state === 'approved')
        <div class="overflow-hidden rounded-2xl border border-emerald-200 bg-emerald-50 p-5 text-center">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-emerald-100 text-2xl">✓</div>
            <h2 class="mt-3 text-base font-bold text-slate-900">Pembayaran disetujui</h2>
            <p class="mt-2 text-xs leading-relaxed text-slate-600">
                Sesi chat aktif {{ $sessionHours ?? 24 }} jam. Silakan mulai chat konsultasi di bawah ini.
            </p>
        </div>



        <div class="fixed inset-x-0 bottom-[4.5rem] z-40 border-t border-slate-200 bg-white/95 px-4 py-3 shadow-[0_-4px_20px_rgba(0,0,0,0.06)] backdrop-blur-sm sm:bottom-0">
            <a
                href="{{ $chatUrl }}"
                class="flex w-full items-center justify-center gap-2.5 rounded-2xl bg-brand-600 py-4 text-sm font-bold text-white shadow-lg shadow-brand-600/25 transition hover:bg-brand-700 active:scale-[0.98]"
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                </svg>
                Mulai Chat Konsultasi
            </a>
        </div>

    @elseif ($state === 'rejected')
        <div class="overflow-hidden rounded-2xl border border-rose-200 bg-rose-50 p-5 text-center">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-rose-100 text-2xl">✕</div>
            <h2 class="mt-3 text-base font-bold text-slate-900">Pembayaran ditolak</h2>
            <p class="mt-2 text-xs leading-relaxed text-slate-600">
                @if ($order?->admin_note)
                    {{ $order->admin_note }}
                @else
                    Transfer tidak ditemukan atau nominal tidak sesuai. Silakan coba lagi.
                @endif
            </p>
        </div>

        <div class="fixed inset-x-0 bottom-[4.5rem] z-40 border-t border-slate-200 bg-white/95 px-4 py-3 shadow-[0_-4px_20px_rgba(0,0,0,0.06)] backdrop-blur-sm sm:bottom-0">
            <a
                href="{{ $retryUrl }}"
                class="flex w-full items-center justify-center gap-2 rounded-2xl bg-rose-500 py-4 text-sm font-bold text-white shadow-sm transition hover:bg-rose-600 active:scale-[0.98]"
            >
                Bayar ulang · {{ $priceLabel }}
            </a>
        </div>
    @endif
</div>

@if ($state === 'pending' && ! empty($pollUrl))
    @push('scripts')
    <script>
        (function () {
            const pollUrl = @json($pollUrl);
            const intervalMs = 15000;

            async function poll() {
                try {
                    const res = await fetch(pollUrl, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    });
                    if (! res.ok) return;
                    const data = await res.json();
                    if (data.status === 'approved' || data.status === 'rejected') {
                        window.location.href = data.redirect_url;
                    }
                } catch (_) {}
            }

            setInterval(poll, intervalMs);
        })();
    </script>
    @endpush
@endif
@endsection
