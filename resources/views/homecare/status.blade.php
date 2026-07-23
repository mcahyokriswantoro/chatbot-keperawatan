@extends('layouts.mobile')

@section('title', 'Status Booking Homecare')

@section('content')
<div class="space-y-4">
    {{-- Header --}}
    <header class="relative -mx-4 overflow-hidden bg-gradient-to-br from-[#00529c] via-[#004787] to-[#003366] px-5 py-4 text-white shadow-md sm:mx-0 sm:rounded-3xl">
        <div class="pointer-events-none absolute -right-8 -top-8 h-32 w-32 rounded-full bg-white/10 blur-2xl"></div>
        <div class="relative flex items-center gap-3">
            <a href="{{ route('home') }}" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white/20 transition hover:bg-white/30" aria-label="Kembali ke Beranda">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
            </a>
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-wider text-blue-100">Status Layanan</p>
                <h1 class="text-base font-bold">Detail Booking Homecare</h1>
            </div>
        </div>
    </header>

    @if (session('status'))
        <div class="rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-xs font-semibold text-emerald-800 shadow-sm flex items-center gap-2">
            <span>✨</span>
            <span class="flex-1">{{ session('status') }}</span>
        </div>
    @endif

    {{-- Tracker Card --}}
    <section class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm text-center space-y-4">
        @if ($booking->isPending())
            <span class="text-4xl">⏳</span>
            <div>
                <h2 class="text-sm font-bold text-slate-800">Menunggu Verifikasi Pembayaran</h2>
                <p class="mt-1 text-xs text-slate-500 leading-relaxed">
                    Bukti transfer pembayaran homecare Anda sedang diperiksa oleh admin.
                </p>
            </div>
        @elseif ($booking->isPaid())
            <span class="text-4xl">🗓️</span>
            <div>
                <h2 class="text-sm font-bold text-slate-800">Jadwal Kunjungan Terkonfirmasi</h2>
                <p class="mt-1 text-xs text-slate-500 leading-relaxed">
                    Pembayaran Anda terverifikasi! Perawat kami akan datang ke alamat Anda sesuai jadwal kunjungan.
                </p>
            </div>
        @elseif ($booking->isCompleted())
            <span class="text-4xl">✅</span>
            <div>
                <h2 class="text-sm font-bold text-emerald-600">Kunjungan Perawat Selesai</h2>
                <p class="mt-1 text-xs text-slate-500 leading-relaxed">
                    Perawat telah melakukan kunjungan ke rumah Anda dan menyelesaikan tindakan medis. Terima kasih!
                </p>
            </div>
        @elseif ($booking->isRejected())
            <span class="text-4xl">❌</span>
            <div>
                <h2 class="text-sm font-bold text-rose-600">Booking Ditolak</h2>
                <p class="mt-1 text-xs text-slate-500 leading-relaxed">
                    Pembayaran booking homecare Anda tidak valid atau ditolak oleh admin.
                </p>
                @if ($booking->admin_note)
                    <div class="mt-3 rounded-xl bg-rose-50 border border-rose-100 p-2.5 text-xs text-rose-800 text-left">
                        <strong>Catatan Admin:</strong> {{ $booking->admin_note }}
                    </div>
                @endif
                <div class="mt-4">
                    <a
                        href="{{ route('homecare.payment', $booking) }}"
                        class="inline-flex items-center gap-1.5 rounded-full bg-[#00529c] px-5 py-2 text-xs font-bold text-white transition hover:bg-[#004787] shadow-sm"
                    >
                        Kirim Ulang Bukti Pembayaran
                    </a>
                </div>
            </div>
        @endif

        {{-- Progress Visualizer --}}
        <div class="border-t border-slate-50 pt-4 flex items-center justify-between text-[10px] font-bold text-slate-400 relative px-4">
            <div class="absolute top-[26px] left-10 right-10 h-0.5 bg-slate-200 -z-10"></div>
            
            <div class="flex flex-col items-center gap-1">
                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-[#00529c] text-white ring-4 ring-[#00529c]/10 text-[10px]">1</span>
                <span class="text-slate-800">Dipesan</span>
            </div>
            
            <div class="flex flex-col items-center gap-1">
                <span @class([
                    'flex h-6 w-6 items-center justify-center rounded-full text-[10px]',
                    'bg-[#00529c] text-white ring-4 ring-[#00529c]/10' => !$booking->isPending() && !$booking->isRejected(),
                    'bg-amber-500 text-white ring-4 ring-amber-500/10' => $booking->isPending(),
                    'bg-rose-500 text-white ring-4 ring-rose-500/10' => $booking->isRejected(),
                ])>2</span>
                <span @class([
                    'text-slate-800' => $booking->isPending() || $booking->isRejected() || $booking->isPaid() || $booking->isCompleted(),
                ])>
                    @if ($booking->isRejected())
                        Ditolak
                    @else
                        Disetujui
                    @endif
                </span>
            </div>

            <div class="flex flex-col items-center gap-1">
                <span @class([
                    'flex h-6 w-6 items-center justify-center rounded-full text-[10px]',
                    'bg-[#00529c] text-white ring-4 ring-[#00529c]/10' => $booking->isCompleted(),
                    'bg-slate-200 text-slate-500' => !$booking->isCompleted(),
                ])>3</span>
                <span @class([
                    'text-slate-800' => $booking->isCompleted(),
                ])>Selesai</span>
            </div>
        </div>
    </section>

    {{-- Detail Booking --}}
    <section class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm space-y-3">
        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Ringkasan Kunjungan</h2>
        <dl class="space-y-2.5 text-xs">
            <div class="flex justify-between gap-3">
                <dt class="text-slate-500">ID Booking</dt>
                <dd class="font-mono font-bold text-slate-800">{{ $booking->reference_code }}</dd>
            </div>
            <div class="flex justify-between gap-3">
                <dt class="text-slate-500">Tanggal Booking</dt>
                <dd class="font-medium text-slate-800">{{ $booking->created_at->translatedFormat('d F Y, H:i') }} WIB</dd>
            </div>
            <div class="flex justify-between gap-3">
                <dt class="text-slate-500">Paket Layanan</dt>
                <dd class="font-semibold text-slate-900">{{ $booking->package->name }}</dd>
            </div>
            <div class="flex justify-between gap-3">
                <dt class="text-slate-500">Jadwal Kunjungan</dt>
                <dd class="font-bold text-slate-900">{{ $booking->booking_date->translatedFormat('d F Y, H:i') }} WIB</dd>
            </div>
            <div class="flex justify-between gap-3">
                <dt class="text-slate-500">Pasien</dt>
                <dd class="font-semibold text-slate-850">{{ $booking->patient_name }} ({{ $booking->patient_phone }})</dd>
            </div>
            <div class="flex justify-between gap-3">
                <dt class="text-slate-500">Alamat Lengkap</dt>
                <dd class="font-medium text-slate-850 text-right max-w-[60%]">{{ $booking->address }}</dd>
            </div>
            @if ($booking->distance_km !== null)
                <div class="flex justify-between gap-3">
                    <dt class="text-slate-500">Jarak Kunjungan</dt>
                    <dd class="font-medium text-slate-800">{{ $booking->distance_km }} km</dd>
                </div>
                <div class="flex justify-between gap-3">
                    <dt class="text-slate-500">Biaya Transport</dt>
                    <dd class="font-medium text-slate-800">Rp {{ number_format($booking->transport_fee ?? 0, 0, ',', '.') }}</dd>
                </div>
            @endif
            <div class="flex justify-between gap-3">
                <dt class="text-slate-500">Pengirim</dt>
                <dd class="font-medium text-slate-800">{{ $booking->sender_identity ?? '—' }}</dd>
            </div>
            <div class="flex justify-between gap-3 border-t border-slate-100 pt-2.5 text-sm font-bold">
                <dt class="text-slate-800">Total Biaya</dt>
                <dd class="text-[#00529c]">{{ $priceLabel }}</dd>
            </div>
        </dl>
    </section>

    <a
        href="{{ route('homecare.index') }}"
        class="flex w-full items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white py-3.5 text-xs font-bold text-slate-700 hover:bg-slate-50 transition active:scale-[0.98]"
    >
        Kembali ke Layanan Homecare
    </a>
</div>
@endsection
