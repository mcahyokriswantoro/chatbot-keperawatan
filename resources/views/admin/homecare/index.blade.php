@extends('layouts.admin')

@section('title', 'Kelola Layanan Homecare')

@section('content')
<div x-data="{ activeTab: '{{ request()->has('status') ? 'bookings' : 'packages' }}' }" class="space-y-4">
    <x-admin.page-banner
        title="Kelola Layanan Homecare"
        subtitle="Kelola paket homecare perawat dan verifikasi kunjungan pasien"
        tone="blue"
        :back="route('admin.dashboard')"
    />

    {{-- Tabs --}}
    <div class="flex rounded-2xl bg-slate-100 p-1">
        <button
            type="button"
            @click="activeTab = 'packages'"
            :class="activeTab === 'packages' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-800'"
            class="flex-1 rounded-xl py-2.5 text-center text-xs font-bold transition"
        >
            Paket Layanan
        </button>
        <button
            type="button"
            @click="activeTab = 'bookings'"
            :class="activeTab === 'bookings' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-800'"
            class="flex-1 rounded-xl py-2.5 text-center text-xs font-bold transition flex items-center justify-center gap-1.5"
        >
            Kunjungan Booking
            @if ($pendingCount > 0)
                <span class="rounded-full bg-rose-500 px-1.5 py-0.5 text-[9px] text-white">{{ $pendingCount }}</span>
            @endif
        </button>
    </div>

    {{-- Tab 1: Packages List --}}
    <div x-show="activeTab === 'packages'" class="space-y-4">
        {{-- Settings Box --}}
        <section class="rounded-2xl border border-brand-100 bg-brand-50/20 p-4 shadow-sm ring-1 ring-brand-100/50">
            <form method="POST" action="{{ route('admin.homecare.settings.update') }}" class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                @csrf
                <div class="min-w-0">
                    <h3 class="text-xs font-bold text-slate-800 leading-snug">⚙️ Tarif Transportasi Homecare</h3>
                    <p class="text-[10px] text-slate-500 mt-0.5 leading-normal">Tentukan biaya transport tambahan per kilometer jarak kunjungan dari Kampus UMLA terdekat.</p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <div class="relative rounded-xl shadow-sm">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 text-xs font-semibold">Rp</span>
                        <input
                            type="number"
                            name="transport_fee_per_km"
                            value="{{ $transportFeePerKm }}"
                            min="0"
                            required
                            class="w-32 rounded-xl border border-slate-200 bg-white py-2 pl-8 pr-3 text-xs font-bold text-slate-800 focus:border-[#00529c] focus:outline-none focus:ring-2 focus:ring-[#00529c]/15"
                        />
                        <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-450 text-[10px] font-bold">/ KM</span>
                    </div>
                    <button
                        type="submit"
                        class="rounded-xl bg-[#00529c] px-4 py-2 text-xs font-bold text-white hover:bg-[#004787] shadow-sm transition active:scale-[0.98]"
                    >
                        Simpan
                    </button>
                </div>
            </form>
        </section>
        <div class="flex items-center justify-between">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Total: {{ $packages->count() }} Paket</h2>
            <a
                href="{{ route('admin.homecare.create') }}"
                class="inline-flex items-center gap-1 rounded-full bg-[#00529c] px-3.5 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-[#004787]"
            >
                + Tambah Paket
            </a>
        </div>

        <div class="space-y-2.5">
            @forelse ($packages as $pkg)
                <section class="rounded-2xl border border-slate-100 bg-white p-3 shadow-sm flex gap-3 items-center justify-between">
                    <div class="flex gap-2.5 items-center min-w-0">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-[#00529c]/5 text-2xl">
                            {{ $pkg->icon }}
                        </span>
                        <div class="min-w-0">
                            <h3 class="truncate text-xs font-bold text-slate-800 leading-snug">{{ $pkg->name }}</h3>
                            <div class="flex items-center gap-2 mt-0.5">
                                @if (!$pkg->active)
                                    <span class="text-[9px] font-bold text-slate-400 bg-slate-100 px-1 rounded uppercase">Nonaktif</span>
                                @else
                                    <span class="text-[9px] font-bold text-emerald-600 bg-emerald-50 px-1 rounded uppercase">Aktif</span>
                                @endif
                                <span class="text-[10px] text-slate-500 font-medium max-w-[150px] truncate" title="{{ $pkg->description }}">{{ $pkg->description }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2.5">
                        <span class="text-xs font-extrabold text-slate-800">Rp {{ number_format($pkg->price, 0, ',', '.') }}</span>
                        
                        <div class="flex gap-1.5">
                            <a
                                href="{{ route('admin.homecare.edit', $pkg) }}"
                                class="flex h-7 w-7 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 hover:bg-slate-50"
                                title="Edit"
                            >
                                ✏️
                            </a>
                            <form method="POST" action="{{ route('admin.homecare.destroy', $pkg) }}" onsubmit="return confirm('Hapus paket homecare {{ $pkg->name }}?')">
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    class="flex h-7 w-7 items-center justify-center rounded-lg border border-slate-200 bg-white text-rose-500 hover:bg-rose-50"
                                    title="Hapus"
                                >
                                    🗑️
                                </button>
                            </form>
                        </div>
                    </div>
                </section>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-12 text-center text-xs text-slate-500">
                    Katalog paket homecare kosong. Silakan tambah paket baru.
                </div>
            @endforelse
        </div>
    </div>

    {{-- Tab 2: Bookings List --}}
    <div x-show="activeTab === 'bookings'" class="space-y-3">
        {{-- Status Filter Bar --}}
        <div class="flex gap-1.5 overflow-x-auto pb-1.5">
            @foreach (['all' => 'Semua', 'pending' => 'Menunggu', 'paid' => 'Disetujui', 'completed' => 'Selesai', 'rejected' => 'Ditolak'] as $key => $label)
                <a
                    href="{{ route('admin.homecare.index', ['status' => $key]) }}"
                    :class="'{{ $status }}' === '{{ $key }}' ? 'bg-[#00529c] text-white' : 'border border-slate-200 bg-white text-slate-600'"
                    class="shrink-0 rounded-full px-3 py-1.5 text-[10px] font-bold transition shadow-sm"
                >
                    {{ $label }}
                    @if ($key === 'pending' && $pendingCount > 0)
                        <span class="ml-1 rounded-full bg-rose-500 px-1 py-0.5 text-[8px] text-white">{{ $pendingCount }}</span>
                    @endif
                </a>
            @endforeach
        </div>

        <div class="space-y-3">
            @forelse ($bookings as $bk)
                <section class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm space-y-3">
                    {{-- Booking Header --}}
                    <div class="flex justify-between items-start border-b border-slate-50 pb-2">
                        <div>
                            <p class="font-mono text-[10px] font-bold text-slate-800">{{ $bk->reference_code }}</p>
                            <p class="text-[9px] text-slate-400 mt-0.5">Dibuat: {{ $bk->created_at->translatedFormat('d F Y, H:i') }}</p>
                        </div>
                        <span @class([
                            'rounded-full px-2.5 py-0.5 text-[9px] font-bold uppercase tracking-wide',
                            'bg-amber-100 text-amber-800' => $bk->isPending(),
                            'bg-emerald-100 text-emerald-800' => $bk->isPaid(),
                            'bg-blue-100 text-blue-800' => $bk->isCompleted(),
                            'bg-rose-100 text-rose-800' => $bk->isRejected(),
                        ])>
                            @if ($bk->isPending())
                                Menunggu Verifikasi
                            @elseif ($bk->isPaid())
                                Terkonfirmasi
                            @elseif ($bk->isCompleted())
                                Selesai
                            @elseif ($bk->isRejected())
                                Ditolak
                            @endif
                        </span>
                    </div>

                    {{-- Booking Details --}}
                    <div class="text-xs space-y-1.5 text-slate-600">
                        <p class="flex justify-between">
                            <span class="text-slate-400">Layanan Paket:</span>
                            <span class="font-semibold text-slate-800">{{ $bk->package->name }}</span>
                        </p>
                        <p class="flex justify-between">
                            <span class="text-slate-400">Jadwal Kunjungan:</span>
                            <span class="font-bold text-slate-800">{{ $bk->booking_date->translatedFormat('d F Y, H:i') }} WIB</span>
                        </p>
                        <p class="flex justify-between">
                            <span class="text-slate-400">Nama Pasien:</span>
                            <span class="font-semibold text-slate-850">{{ $bk->patient_name }} ({{ $bk->patient_phone }})</span>
                        </p>
                        <p class="flex justify-between">
                            <span class="text-slate-400">Alamat Kunjungan:</span>
                            <span class="font-medium text-slate-800 text-right max-w-[65%] leading-relaxed">
                                {{ $bk->address }}
                                @if ($bk->latitude && $bk->longitude)
                                    <br>
                                    <a href="https://www.google.com/maps/search/?api=1&query={{ $bk->latitude }},{{ $bk->longitude }}" target="_blank" class="inline-flex items-center gap-0.5 text-[10px] font-bold text-[#00529c] underline mt-0.5">
                                        📍 Buka Google Maps
                                    </a>
                                @endif
                            </span>
                        </p>
                        @if ($bk->distance_km !== null)
                            <p class="flex justify-between border-t border-dashed border-slate-100 pt-1.5">
                                <span class="text-slate-400">Jarak Kunjungan:</span>
                                <span class="font-semibold text-slate-850">{{ $bk->distance_km }} km</span>
                            </p>
                            <p class="flex justify-between">
                                <span class="text-slate-400">Biaya Transport:</span>
                                <span class="font-semibold text-slate-850">Rp {{ number_format($bk->transport_fee ?? 0, 0, ',', '.') }}</span>
                            </p>
                        @endif
                        <p class="flex justify-between border-t border-slate-100 pt-2 text-sm font-bold">
                            <span class="text-slate-800">Total Biaya:</span>
                            <span class="text-[#00529c]">Rp {{ number_format($bk->totalPrice(), 0, ',', '.') }}</span>
                        </p>
                    </div>

                    {{-- Receipt / Sender info if exists --}}
                    @if ($bk->sender_identity || $bk->payment_proof)
                        <div class="rounded-xl bg-slate-50 p-2.5 text-xs text-slate-600 space-y-2 border border-slate-100">
                            @if ($bk->sender_identity)
                                <p class="flex justify-between">
                                    <span class="text-slate-400">Pengirim transfer:</span>
                                    <span class="font-mono font-semibold text-slate-700">{{ $bk->sender_identity }}</span>
                                </p>
                            @endif
                            @if ($bk->payment_proof)
                                <div class="flex justify-between items-center gap-2">
                                    <span class="text-slate-400">Bukti Transfer:</span>
                                    <a
                                        href="{{ $bk->paymentProofUrl() }}"
                                        target="_blank"
                                        class="inline-flex items-center gap-1 text-[11px] font-bold text-[#00529c] underline"
                                    >
                                        📂 Lihat Bukti
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if ($bk->admin_note)
                        <div class="rounded-xl bg-rose-50 border border-rose-100 p-2.5 text-xs text-rose-800">
                            <strong>Catatan Penolakan:</strong> {{ $bk->admin_note }}
                        </div>
                    @endif

                    {{-- Actions --}}
                    @if ($bk->isPending() && $bk->payment_proof)
                        <div class="flex gap-2 pt-2 border-t border-slate-50" x-data="{ showReject: false }">
                            <div class="w-full space-y-3" x-show="!showReject">
                                <div class="flex gap-2">
                                    <button
                                        type="button"
                                        @click="showReject = true"
                                        class="flex-1 rounded-xl border border-rose-200 py-2.5 text-center text-xs font-bold text-rose-600 hover:bg-rose-50 transition"
                                    >
                                        Tolak
                                    </button>
                                    <form method="POST" action="{{ route('admin.homecare.bookings.approve', $bk) }}" class="flex-1">
                                        @csrf
                                        <button
                                            type="submit"
                                            class="w-full rounded-xl bg-[#00529c] py-2.5 text-center text-xs font-bold text-white hover:bg-[#004787] shadow-sm transition"
                                        >
                                            Setujui Kunjungan
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('admin.homecare.bookings.reject', $bk) }}" class="w-full space-y-2.5" x-show="showReject" x-cloak>
                                @csrf
                                <div>
                                    <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Alasan Penolakan</label>
                                    <input
                                        type="text"
                                        name="admin_note"
                                        required
                                        placeholder="Contoh: Bukti transfer tidak terbaca / nominal tidak cocok"
                                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs focus:border-rose-400 focus:outline-none focus:ring-2 focus:ring-rose-100"
                                    >
                                </div>
                                <div class="flex gap-2">
                                    <button
                                        type="button"
                                        @click="showReject = false"
                                        class="flex-1 rounded-xl border border-slate-200 py-2 text-center text-xs font-bold text-slate-500 hover:bg-slate-50 transition"
                                    >
                                        Batal
                                    </button>
                                    <button
                                        type="submit"
                                        class="flex-1 rounded-xl bg-rose-600 py-2 text-center text-xs font-bold text-white hover:bg-rose-700 shadow-sm transition"
                                    >
                                        Kirim Tolak
                                    </button>
                                </div>
                            </form>
                        </div>
                    @elseif ($bk->isPaid())
                        <form method="POST" action="{{ route('admin.homecare.bookings.complete', $bk) }}" class="pt-2 border-t border-slate-50">
                            @csrf
                            <button
                                type="submit"
                                class="w-full rounded-xl bg-emerald-650 py-2.5 text-center text-xs font-bold text-white hover:bg-emerald-700 shadow-sm transition flex items-center justify-center gap-1.5"
                            >
                                ✅ Tandai Tindakan Kunjungan Selesai
                            </button>
                        </form>
                    @endif
                </section>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-12 text-center text-xs text-slate-500">
                    Tidak ada data booking homecare dengan status ini.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
