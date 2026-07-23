@extends('layouts.admin')

@section('title', 'Kelola Obat & Vitamin')

@section('content')
<div x-data="{ activeTab: '{{ request()->has('status') ? 'orders' : 'medicines' }}' }" class="space-y-4">
    <x-admin.page-banner
        title="Kelola Obat & Vitamin"
        subtitle="Kelola stok obat dan verifikasi pembelian dari pasien"
        tone="blue"
        :back="route('admin.dashboard')"
    />

    {{-- Tabs --}}
    <div class="flex rounded-2xl bg-slate-100 p-1">
        <button
            type="button"
            @click="activeTab = 'medicines'"
            :class="activeTab === 'medicines' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-800'"
            class="flex-1 rounded-xl py-2.5 text-center text-xs font-bold transition"
        >
            Daftar Obat
        </button>
        <button
            type="button"
            @click="activeTab = 'orders'"
            :class="activeTab === 'orders' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-800'"
            class="flex-1 rounded-xl py-2.5 text-center text-xs font-bold transition flex items-center justify-center gap-1.5"
        >
            Pesanan Masuk
            @if ($pendingCount > 0)
                <span class="rounded-full bg-rose-500 px-1.5 py-0.5 text-[9px] text-white">{{ $pendingCount }}</span>
            @endif
        </button>
    </div>

    {{-- Tab 1: Medicines List --}}
    <div x-show="activeTab === 'medicines'" class="space-y-3">
        {{-- Settings Box --}}
        <section class="rounded-2xl border border-brand-100 bg-brand-50/20 p-4 shadow-sm ring-1 ring-brand-100/50">
            <form method="POST" action="{{ route('admin.medicines.settings.update') }}" class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                @csrf
                <div class="min-w-0">
                    <h3 class="text-xs font-bold text-slate-800 leading-snug">⚙️ Tarif Pengiriman Obat</h3>
                    <p class="text-[10px] text-slate-500 mt-0.5 leading-normal">Tentukan tarif pengiriman obat tambahan per kilometer jarak dari Apotek Pusat UMLA Kampus 1.</p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <div class="relative rounded-xl shadow-sm">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 text-xs font-semibold">Rp</span>
                        <input
                            type="number"
                            name="medicine_shipping_fee_per_km"
                            value="{{ $shippingFeePerKm }}"
                            min="0"
                            required
                            class="w-32 rounded-xl border border-slate-200 bg-white py-2 pl-8 pr-3 text-xs font-bold text-slate-800 focus:border-[#00529c] focus:outline-none focus:ring-2 focus:ring-[#00529c]/15"
                        />
                        <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-450 text-[10px] font-bold">/ KM</span>
                    </div>
                    <button
                        type="submit"
                        class="rounded-xl bg-[#00529c] px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-[#004787] transition active:scale-95"
                    >
                        Simpan
                    </button>
                </div>
            </form>
        </section>

        <div class="flex items-center justify-between">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Total: {{ $medicines->count() }} Obat</h2>
            <a
                href="{{ route('admin.medicines.create') }}"
                class="inline-flex items-center gap-1 rounded-full bg-[#00529c] px-3.5 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-[#004787]"
            >
                + Tambah Obat
            </a>
        </div>

        <div class="space-y-2.5">
            @forelse ($medicines as $med)
                <section class="rounded-2xl border border-slate-100 bg-white p-3 shadow-sm flex gap-3 items-center justify-between">
                    <div class="flex gap-2.5 items-center min-w-0">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-slate-50 border border-slate-100 overflow-hidden">
                            <img src="{{ $med->photoUrl() }}" alt="" class="max-h-9 max-w-full object-contain">
                        </div>
                        <div class="min-w-0">
                            <h3 class="truncate text-xs font-bold text-slate-800 leading-snug">{{ $med->name }}</h3>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="text-[9px] font-bold text-[#00529c] bg-[#00529c]/5 px-1 rounded">{{ $med->category }}</span>
                                <span class="text-[10px] text-slate-500 font-medium">Stok: {{ $med->stock }}</span>
                                @if (!$med->active)
                                    <span class="text-[9px] font-bold text-slate-400 bg-slate-100 px-1 rounded uppercase">Nonaktif</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2.5">
                        <span class="text-xs font-extrabold text-slate-800">Rp {{ number_format($med->price, 0, ',', '.') }}</span>
                        
                        <div class="flex gap-1.5">
                            <a
                                href="{{ route('admin.medicines.edit', $med) }}"
                                class="flex h-7 w-7 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 hover:bg-slate-50"
                                title="Edit"
                            >
                                ✏️
                            </a>
                            <form method="POST" action="{{ route('admin.medicines.destroy', $med) }}" onsubmit="return confirm('Hapus obat {{ $med->name }} dari katalog?')">
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
                    Katalog obat kosong. Silakan tambah obat baru.
                </div>
            @endforelse
        </div>
    </div>

    {{-- Tab 2: Orders List --}}
    <div x-show="activeTab === 'orders'" class="space-y-3">
        {{-- Status Filter Bar --}}
        <div class="flex gap-1.5 overflow-x-auto pb-1.5">
            @foreach (['all' => 'Semua', 'pending' => 'Menunggu', 'paid' => 'Lunas', 'delivered' => 'Dikirim', 'rejected' => 'Ditolak'] as $key => $label)
                <a
                    href="{{ route('admin.medicines.index', ['status' => $key]) }}"
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
            @forelse ($orders as $order)
                <section class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm space-y-3">
                    {{-- Order Header --}}
                    <div class="flex justify-between items-start border-b border-slate-50 pb-2">
                        <div>
                            <p class="font-mono text-[10px] font-bold text-slate-800">{{ $order->reference_code }}</p>
                            <p class="text-[9px] text-slate-400 mt-0.5">{{ $order->created_at->translatedFormat('d F Y, H:i') }}</p>
                        </div>
                        <span @class([
                            'rounded-full px-2.5 py-0.5 text-[9px] font-bold uppercase tracking-wide',
                            'bg-amber-100 text-amber-800' => $order->isPending(),
                            'bg-emerald-100 text-emerald-800' => $order->isPaid(),
                            'bg-blue-100 text-blue-800' => $order->isDelivered(),
                            'bg-rose-100 text-rose-800' => $order->isRejected(),
                        ])>
                            @if ($order->isPending())
                                Menunggu Verifikasi
                            @elseif ($order->isPaid())
                                Lunas (Siap Kirim)
                            @elseif ($order->isDelivered())
                                Dikirim
                            @elseif ($order->isRejected())
                                Ditolak
                            @endif
                        </span>
                    </div>

                    {{-- Order Details --}}
                    <div class="text-xs space-y-1.5 text-slate-600">
                        <p class="flex justify-between">
                            <span class="text-slate-400">Pembeli:</span>
                            <span class="font-semibold text-slate-800">{{ $order->user->name }}</span>
                        </p>
                        <div class="flex justify-between items-start">
                            <span class="text-slate-400 shrink-0">Item:</span>
                            <span class="font-medium text-slate-800 text-right max-w-[70%]">
                                @foreach ($order->items as $item)
                                    {{ $item->medicine->name }} (x{{ $item->quantity }})<br>
                                @endforeach
                            </span>
                        </div>
                        <p class="flex justify-between">
                            <span class="text-slate-400">Alamat Kirim:</span>
                            <span class="font-medium text-slate-800 text-right max-w-[70%] line-clamp-2" title="{{ $order->address }}">{{ $order->address }}</span>
                        </p>
                        @if ($order->closest_pharmacy)
                            <p class="flex justify-between">
                                <span class="text-slate-400">Apotek Pengirim:</span>
                                <span class="font-semibold text-slate-800">{{ $order->closest_pharmacy }}</span>
                            </p>
                        @endif
                        @if ($order->distance_km)
                            <p class="flex justify-between">
                                <span class="text-slate-400">Jarak Kirim:</span>
                                <span class="font-semibold text-slate-800">{{ $order->distance_km }} KM</span>
                            </p>
                            <p class="flex justify-between">
                                <span class="text-slate-400">Ongkos Kirim:</span>
                                <span class="font-semibold text-slate-800">Rp {{ number_format($order->shipping_fee, 0, ',', '.') }}</span>
                            </p>
                        @endif
                        @if ($order->latitude && $order->longitude)
                            <p class="flex justify-between items-center">
                                <span class="text-slate-400">Rute Pengantaran:</span>
                                <a
                                    href="https://www.google.com/maps/search/?api=1&query={{ $order->latitude }},{{ $order->longitude }}"
                                    target="_blank"
                                    class="inline-flex items-center gap-1 text-[11px] font-bold text-[#00529c] underline"
                                >
                                    📍 Buka di Google Maps
                                </a>
                            </p>
                        @endif
                        <p class="flex justify-between border-t border-slate-50 pt-2 text-sm font-bold">
                            <span class="text-slate-800">Total Pembayaran:</span>
                            <span class="text-[#00529c]">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        </p>
                    </div>

                    {{-- Receipt / Sender info if exists --}}
                    @if ($order->sender_identity || $order->payment_proof)
                        <div class="rounded-xl bg-slate-50 p-2.5 text-xs text-slate-600 space-y-2 border border-slate-100">
                            @if ($order->sender_identity)
                                <p class="flex justify-between">
                                    <span class="text-slate-400">Pengirim transfer:</span>
                                    <span class="font-mono font-semibold text-slate-700">{{ $order->sender_identity }}</span>
                                </p>
                            @endif
                            @if ($order->payment_proof)
                                <div class="flex justify-between items-center gap-2">
                                    <span class="text-slate-400">Bukti Transfer:</span>
                                    <a
                                        href="{{ $order->paymentProofUrl() }}"
                                        target="_blank"
                                        class="inline-flex items-center gap-1 text-[11px] font-bold text-[#00529c] underline"
                                    >
                                        📂 Lihat Bukti
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if ($order->admin_note)
                        <div class="rounded-xl bg-rose-50 border border-rose-100 p-2.5 text-xs text-rose-800">
                            <strong>Catatan Penolakan:</strong> {{ $order->admin_note }}
                        </div>
                    @endif

                    {{-- Actions --}}
                    @if ($order->isPending() && $order->payment_proof)
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
                                    <form method="POST" action="{{ route('admin.medicines.orders.approve', $order) }}" class="flex-1">
                                        @csrf
                                        <button
                                            type="submit"
                                            class="w-full rounded-xl bg-[#00529c] py-2.5 text-center text-xs font-bold text-white hover:bg-[#004787] shadow-sm transition"
                                        >
                                            Setujui
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('admin.medicines.orders.reject', $order) }}" class="w-full space-y-2.5" x-show="showReject" x-cloak>
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
                    @elseif ($order->isPaid())
                        <form method="POST" action="{{ route('admin.medicines.orders.deliver', $order) }}" class="pt-2 border-t border-slate-50">
                            @csrf
                            <button
                                type="submit"
                                class="w-full rounded-xl bg-emerald-600 py-2.5 text-center text-xs font-bold text-white hover:bg-emerald-700 shadow-sm transition flex items-center justify-center gap-1.5"
                            >
                                🛵 Tandai Pesanan Telah Dikirim
                            </button>
                        </form>
                    @endif
                </section>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-12 text-center text-xs text-slate-500">
                    Tidak ada transaksi pemesanan obat dengan status ini.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
