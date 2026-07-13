@extends('layouts.mobile')

@section('content')
@php
    $photoUrl = $provider['photo'] ?? asset('images/avatars/male.svg');
    $providerName = $provider['short_name'] ?? $provider['name'];
@endphp

<div class="space-y-4 pb-36">
    {{-- Header WhatsApp --}}
    <header class="relative -mx-4 overflow-hidden bg-gradient-to-br from-[#075E54] via-[#128C7E] to-[#25D366] px-5 pb-6 pt-2 text-white shadow-lg sm:mx-0 sm:rounded-3xl">
        <div class="pointer-events-none absolute -right-8 -top-8 h-32 w-32 rounded-full bg-white/10 blur-2xl"></div>
        <div class="relative flex items-center gap-2">
            <a
                href="{{ $checkoutUrl }}"
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white/20 transition hover:bg-white/30"
                aria-label="Kembali"
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
            </a>
            <div class="min-w-0 flex-1">
                <p class="text-[10px] font-semibold uppercase tracking-wider text-emerald-100">WhatsApp Live</p>
                <h1 class="truncate text-base font-bold">Chat dengan {{ $providerName }}</h1>
            </div>
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-white/20 text-xl backdrop-blur-sm">💬</span>
        </div>
        <p class="relative mt-3 text-xs leading-relaxed text-white/90">
            Pembayaran sudah diverifikasi. Sesi chat aktif {{ $sessionHours }} jam — lanjut ke WhatsApp untuk konsultasi live.
        </p>
    </header>

    {{-- Kartu perawat --}}
    <div class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white p-3 shadow-sm">
        <div class="h-14 w-12 shrink-0 overflow-hidden rounded-xl bg-slate-100 ring-1 ring-slate-200">
            <img src="{{ $photoUrl }}" alt="{{ $providerName }}" class="h-full w-full object-cover object-top">
        </div>
        <div class="min-w-0 flex-1">
            <p class="truncate text-sm font-bold text-slate-900">{{ $provider['name'] ?? $providerName }}</p>
            <p class="text-xs text-slate-500">{{ $provider['specialty'] ?? 'Konsultasi' }}</p>
            @if ($whatsappDisplayNumber)
                <p class="mt-0.5 text-[10px] font-medium text-[#128C7E]">{{ $whatsappDisplayNumber }}</p>
            @endif
        </div>
        <span class="shrink-0 rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-bold text-emerald-700">Online</span>
    </div>

    {{-- Preview pesan --}}
    <section>
        <p class="mb-2 text-xs font-bold text-slate-700">Pesan yang akan dikirim</p>
        @include('consultation.partials.whatsapp-preview', [
            'preview' => $whatsappPreview,
            'displayNumber' => $whatsappDisplayNumber,
            'providerName' => $providerName,
            'photoUrl' => $photoUrl,
        ])
    </section>

    <div class="rounded-2xl border border-slate-100 bg-slate-50 px-4 py-3 text-[11px] leading-relaxed text-slate-600">
        Tekan tombol di bawah untuk membuka WhatsApp. Pesan di atas sudah terisi — tinggal kirim ke perawat.
    </div>

    <div class="fixed inset-x-0 bottom-[4.5rem] z-40 border-t border-slate-200 bg-white/95 px-4 py-3 shadow-[0_-4px_20px_rgba(0,0,0,0.06)] backdrop-blur-sm sm:bottom-0">
        <a
            href="{{ $whatsappUrl }}"
            target="_blank"
            rel="noopener noreferrer"
            class="flex w-full items-center justify-center gap-2.5 rounded-2xl bg-[#25D366] py-4 text-sm font-bold text-white shadow-lg shadow-[#25D366]/30 transition hover:bg-[#1da851] active:scale-[0.98]"
        >
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.435 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            Buka WhatsApp Live
        </a>
    </div>

    <p class="text-center text-[10px] text-slate-400">Akan membuka WhatsApp · pesan sudah terisi otomatis</p>
</div>
@endsection
