@extends('layouts.admin')

@section('title', 'Voucher Konsultasi')

@section('content')
    <x-admin.page-banner
        title="Voucher Konsultasi"
        subtitle="Buat kode diskon 100%, 50%, atau 25% untuk chat perawat"
        tone="violet"
        :back="route('admin.dashboard')"
    />

    @include('admin.partials.consultation-tabs')

    {{-- Form buat voucher --}}
    <section class="mb-5 rounded-2xl border border-violet-100 bg-violet-50/40 p-4">
        <h2 class="text-sm font-bold text-slate-900">Buat voucher baru</h2>
        <p class="mt-1 text-xs text-slate-600">100% = gratis langsung chat · 50%/25% = user bayar sisa via Transfer</p>

        <form method="POST" action="{{ route('admin.consultations.vouchers.store') }}" class="mt-4 space-y-3">
            @csrf
            <div>
                <label class="mb-1 block text-[11px] font-medium text-slate-500">Kode voucher</label>
                <input
                    type="text"
                    name="code"
                    value="{{ old('code') }}"
                    placeholder="PERAWAT50"
                    required
                    class="w-full rounded-xl border border-brand-200 bg-white px-3 py-2.5 text-sm uppercase focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-100"
                >
                @error('code')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="mb-1 block text-[11px] font-medium text-slate-500">Diskon</label>
                    <select name="discount_percent" required class="w-full rounded-xl border border-brand-200 bg-white px-3 py-2.5 text-sm">
                        @foreach ($discountOptions as $pct)
                            <option value="{{ $pct }}" @selected(old('discount_percent') == $pct)>{{ $pct }}%</option>
                        @endforeach
                    </select>
                    @error('discount_percent')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="mb-1 block text-[11px] font-medium text-slate-500">Maks. pemakaian</label>
                    <input
                        type="number"
                        name="max_uses"
                        value="{{ old('max_uses', 100) }}"
                        min="1"
                        required
                        class="w-full rounded-xl border border-brand-200 bg-white px-3 py-2.5 text-sm"
                    >
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="mb-1 block text-[11px] font-medium text-slate-500">Berlaku untuk</label>
                    <select name="provider_key" class="w-full rounded-xl border border-brand-200 bg-white px-3 py-2.5 text-sm">
                        <option value="">Semua layanan</option>
                        @foreach ($providers as $provider)
                            <option value="{{ $provider['key'] }}" @selected(old('provider_key') === $provider['key'])>{{ $provider['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-[11px] font-medium text-slate-500">Kadaluarsa (opsional)</label>
                    <input
                        type="date"
                        name="expires_at"
                        value="{{ old('expires_at') }}"
                        class="w-full rounded-xl border border-brand-200 bg-white px-3 py-2.5 text-sm"
                    >
                </div>
            </div>

            <button type="submit" class="w-full rounded-xl bg-violet-600 py-3 text-sm font-bold text-white hover:bg-violet-700">
                Simpan voucher
            </button>
        </form>
    </section>

    {{-- Daftar voucher --}}
    <section>
        <h2 class="mb-3 text-sm font-bold text-slate-900">Daftar voucher</h2>
        <div class="space-y-3">
            @forelse ($vouchers as $voucher)
                <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-mono text-sm font-bold text-slate-900">{{ $voucher->code }}</p>
                            <p class="mt-0.5 text-xs text-slate-500">
                                Diskon {{ $voucher->discount_percent }}%
                                · {{ $voucher->uses_count }}/{{ $voucher->max_uses }} dipakai
                            </p>
                        </div>
                        <span @class([
                            'shrink-0 rounded-full px-2.5 py-0.5 text-[10px] font-bold uppercase',
                            'bg-emerald-100 text-emerald-800' => $voucher->is_active,
                            'bg-slate-100 text-slate-500' => ! $voucher->is_active,
                        ])>
                            {{ $voucher->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>

                    <dl class="mt-3 grid gap-1.5 text-xs text-slate-600">
                        <div class="flex justify-between gap-3">
                            <dt>Berlaku</dt>
                            <dd class="font-medium text-slate-900">
                                @if ($voucher->provider_key)
                                    {{ \App\Models\ConsultationProvider::query()->where('key', $voucher->provider_key)->value('short_name') ?? config("consultation.providers.{$voucher->provider_key}.short_name", $voucher->provider_key) }}
                                @else
                                    Semua layanan
                                @endif
                            </dd>
                        </div>
                        @if ($voucher->expires_at)
                            <div class="flex justify-between gap-3">
                                <dt>Kadaluarsa</dt>
                                <dd>{{ $voucher->expires_at->timezone(config('app.timezone'))->format('d M Y') }}</dd>
                            </div>
                        @endif
                    </dl>

                    <div class="mt-4 flex flex-wrap gap-2">
                        <form method="POST" action="{{ route('admin.consultations.vouchers.toggle', $voucher) }}">
                            @csrf
                            <button type="submit" class="rounded-lg border border-slate-200 px-3 py-1.5 text-[11px] font-semibold text-slate-700 hover:bg-slate-50">
                                {{ $voucher->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.consultations.vouchers.destroy', $voucher) }}" onsubmit="return confirm('Hapus voucher {{ $voucher->code }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-[11px] font-semibold text-rose-700 hover:bg-rose-100">
                                Hapus
                            </button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-violet-200 bg-violet-50/30 px-4 py-10 text-center text-sm text-slate-500">
                    Belum ada voucher. Buat kode di form atas.
                </div>
            @endforelse
        </div>

        @if ($vouchers->hasPages())
            <div class="mt-4">{{ $vouchers->links() }}</div>
        @endif
    </section>
@endsection
