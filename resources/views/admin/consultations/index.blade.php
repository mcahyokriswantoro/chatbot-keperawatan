@extends('layouts.admin')

@section('title', 'Konsultasi')

@section('content')
    <x-admin.page-banner
        title="Verifikasi Konsultasi"
        :subtitle="$pendingCount > 0 ? $pendingCount.' pembayaran Giro BRI menunggu verifikasi' : 'Kelola pembayaran chat perawat'"
        tone="emerald"
        :back="route('admin.dashboard')"
    />

    @include('admin.partials.consultation-tabs')

    @php
        $categoriesList = config('consultation.categories', []);
    @endphp

    <div class="mb-4 space-y-2">
        <p class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Pengaturan Status Biaya Per Kategori / Peran:</p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            @foreach ($categoriesList as $cat)
                @php
                    $isCatFree = \App\Models\Setting::getValue("consultation_free_{$cat['key']}", '0') === '1' || \App\Models\Setting::getValue('consultation_is_free', '0') === '1';
                @endphp
                <div class="flex flex-col justify-between gap-3 rounded-2xl border p-4 shadow-sm transition {{ $isCatFree ? 'border-emerald-200 bg-emerald-50/80' : 'border-amber-200 bg-amber-50/80' }}">
                    <div class="flex items-start gap-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $isCatFree ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }} text-xl">
                            {{ $cat['icon'] ?? ($isCatFree ? '🎁' : '💳') }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-bold text-slate-900 truncate">{{ $cat['label'] }}</p>
                            <p class="text-[11px] font-bold mt-0.5 {{ $isCatFree ? 'text-emerald-700' : 'text-amber-700' }}">
                                {{ $isCatFree ? 'GRATIS 100%' : 'HARUS BAYAR (BERBAYAR)' }}
                            </p>
                            <p class="text-[10px] text-slate-500 mt-0.5">
                                {{ $isCatFree ? 'Pasien gratis tanpa biaya.' : 'Pasien wajib bayar / voucher.' }}
                            </p>
                        </div>
                    </div>
                    <form action="{{ route('admin.consultations.toggle-free', $cat['key']) }}" method="POST" class="mt-1">
                        @csrf
                        <button type="submit" class="w-full rounded-xl px-3 py-2 text-xs font-bold text-white shadow-sm transition {{ $isCatFree ? 'bg-amber-600 hover:bg-amber-500' : 'bg-emerald-600 hover:bg-emerald-500' }}">
                            {{ $isCatFree ? 'Ubah ke Harus Bayar' : 'Ubah ke Gratis 100%' }}
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>

    <div class="mb-4 grid grid-cols-3 gap-2">
        <div class="rounded-xl bg-amber-50 px-3 py-2.5 text-center ring-1 ring-amber-100">
            <p class="text-xl font-bold text-amber-700">{{ $pendingCount }}</p>
            <p class="text-[9px] font-medium text-slate-500">Menunggu</p>
        </div>
        <div class="rounded-xl bg-emerald-50 px-3 py-2.5 text-center ring-1 ring-emerald-100">
            <p class="text-xl font-bold text-emerald-700">{{ $paidCount }}</p>
            <p class="text-[9px] font-medium text-slate-500">Disetujui</p>
        </div>
        <div class="rounded-xl bg-rose-50 px-3 py-2.5 text-center ring-1 ring-rose-100">
            <p class="text-xl font-bold text-rose-700">{{ $rejectedCount }}</p>
            <p class="text-[9px] font-medium text-slate-500">Ditolak</p>
        </div>
    </div>

    <div class="mb-4 rounded-2xl border border-sky-100 bg-sky-50/50 px-4 py-3 text-xs leading-relaxed text-slate-600">
        <strong>Cara verifikasi:</strong> cek transfer masuk ke rekening Giro BRI
        <strong>004101003652303</strong> (a.n. Chatbot Keperawatan),
        cocokkan nominal & ID transaksi, lalu <strong>Setujui</strong> agar user bisa chat WhatsApp live.
    </div>

    <div class="mb-4 flex gap-2 overflow-x-auto pb-1">
        @foreach (['pending' => 'Menunggu', 'paid' => 'Disetujui', 'rejected' => 'Ditolak', 'all' => 'Semua'] as $key => $label)
            <a
                href="{{ route('admin.consultations.index', ['status' => $key]) }}"
                @class([
                    'shrink-0 rounded-full px-4 py-2 text-xs font-semibold transition',
                    'bg-emerald-600 text-white' => $status === $key,
                    'border border-slate-200 bg-white text-slate-600' => $status !== $key,
                ])
            >
                {{ $label }}
                @if ($key === 'pending' && $pendingCount > 0)
                    <span @class(['ml-1 rounded-full px-1.5', 'bg-white/20' => $status === $key, 'bg-amber-100 text-amber-800' => $status !== $key])>{{ $pendingCount }}</span>
                @endif
            </a>
        @endforeach
    </div>

    <div class="space-y-3">
        @forelse ($orders as $order)
            @include('admin.partials.consultation-order-card', ['order' => $order])
        @empty
            <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-10 text-center text-sm text-slate-500">
                Tidak ada data pembayaran konsultasi.
            </div>
        @endforelse
    </div>

    @if ($orders->hasPages())
        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    @endif
@endsection
