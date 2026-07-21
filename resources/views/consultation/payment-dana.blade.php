@extends('layouts.mobile')

@section('content')
@php
    $photoUrl = $provider['photo'] ?? asset('images/avatars/male.svg');
    $hasDiscount = ! empty($voucherCode) && ($dueAmount ?? $price) < $price;
@endphp

<div
    x-data="{
        copied: '',
        openBank: 'bri',
        copy(text, key) {
            navigator.clipboard.writeText(text).then(() => {
                this.copied = key;
                setTimeout(() => this.copied = '', 2000);
            });
        },
    }"
    class="space-y-4 pb-6"
>
    {{-- Header BRI --}}
    <header class="relative -mx-4 overflow-hidden bg-gradient-to-br from-[#00529c] via-[#004787] to-[#003366] px-5 pb-5 pt-2 text-white shadow-lg sm:mx-0 sm:rounded-3xl">
        <div class="pointer-events-none absolute -right-8 -top-8 h-32 w-32 rounded-full bg-white/10 blur-2xl"></div>
        <div class="relative flex items-center gap-2">
            <a
                href="{{ route('consultation.checkout', $providerKey) }}"
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white/20 transition hover:bg-white/30"
                aria-label="Kembali"
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
            </a>
            <div class="flex min-w-0 flex-1 items-center gap-2">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-lg font-black text-[#00529c]">B</span>
                <div class="min-w-0">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-blue-100">Pembayaran Giro BRI</p>
                    <h1 class="truncate text-base font-bold">Konfirmasi transfer</h1>
                </div>
            </div>
        </div>

        <div class="relative mt-4">
            <p class="text-xs text-white/80">Total yang harus ditransfer</p>
            <p class="text-3xl font-bold tracking-tight">{{ $priceLabel }}</p>
            @if ($hasDiscount)
                <div class="mt-2 inline-flex flex-wrap items-center gap-2">
                    <span class="rounded-full bg-white/20 px-2.5 py-0.5 text-[10px] font-semibold backdrop-blur-sm">Voucher {{ $voucherCode }}</span>
                    <span class="text-xs text-white/70 line-through">{{ $originalPriceLabel }}</span>
                </div>
            @endif
        </div>
    </header>

    @if (session('status'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('status') }}
        </div>
    @endif

    {{-- Ringkasan perawat --}}
    <div class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white p-3 shadow-sm">
        <div class="h-14 w-12 shrink-0 overflow-hidden rounded-xl bg-slate-100 ring-1 ring-slate-200">
            <img src="{{ $photoUrl }}" alt="{{ $provider['short_name'] ?? '' }}" class="h-full w-full object-cover object-top">
        </div>
        <div class="min-w-0 flex-1">
            <p class="truncate text-sm font-bold text-slate-900">{{ $provider['short_name'] ?? $provider['name'] }}</p>
            <p class="text-xs text-slate-500">{{ $provider['specialty'] ?? 'Konsultasi' }}</p>
        </div>
        <span class="shrink-0 rounded-full bg-sky-50 px-2.5 py-1 text-[10px] font-bold text-sky-700">Chat live</span>
    </div>

    {{-- Progress langkah --}}
    <div class="grid grid-cols-3 gap-2">
        <div class="rounded-xl border border-[#00529c]/30 bg-[#00529c]/5 px-2 py-2.5 text-center">
            <p class="text-[10px] font-bold text-[#00529c]">1</p>
            <p class="mt-0.5 text-[9px] font-semibold leading-tight text-slate-700">Transfer</p>
        </div>
        <div class="rounded-xl border border-[#00529c]/30 bg-[#00529c]/10 px-2 py-2.5 text-center ring-2 ring-[#00529c]/20">
            <p class="text-[10px] font-bold text-[#00529c]">2</p>
            <p class="mt-0.5 text-[9px] font-semibold leading-tight text-slate-700">Konfirmasi</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-slate-50 px-2 py-2.5 text-center">
            <p class="text-[10px] font-bold text-slate-400">3</p>
            <p class="mt-0.5 text-[9px] font-semibold leading-tight text-slate-500">Verifikasi</p>
        </div>
    </div>

    {{-- Nomor Rekening BRI tujuan --}}
    <section class="overflow-hidden rounded-2xl border border-[#00529c]/25 bg-gradient-to-b from-[#00529c]/5 to-white shadow-sm">
        <div class="border-b border-[#00529c]/10 px-4 py-3">
            <h2 class="text-sm font-bold text-slate-900">Transfer ke Giro BRI</h2>
            <p class="mt-0.5 text-xs text-slate-500">Kirim transfer antar bank ke rekening Giro BRI di bawah</p>
        </div>
        <div class="p-4">
            <div class="rounded-2xl border-2 border-dashed border-[#00529c]/30 bg-white px-4 py-4 text-center">
                <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Nomor Rekening BRI</p>
                <p class="mt-1 text-2xl font-bold tracking-wide text-[#00529c]">004101003652303</p>
                <p class="mt-0.5 text-xs font-semibold text-slate-600">Giro BRI</p>
                <button
                    type="button"
                    @click="copy('004101003652303', 'rekening')"
                    class="mt-3 inline-flex items-center gap-1.5 rounded-full border border-[#00529c]/30 bg-[#00529c]/5 px-4 py-1.5 text-[11px] font-semibold text-[#00529c] transition hover:bg-[#00529c]/10"
                >
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0013.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 01-.75.75H9.75a.75.75 0 01-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 011.927-.184"/></svg>
                    <span x-text="copied === 'rekening' ? 'Tersalin!' : 'Salin Rekening'"></span>
                </button>
            </div>

            <ul class="mt-4 space-y-2 text-xs leading-relaxed text-slate-600">
                <li class="flex gap-2">
                    <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-[#00529c]/10 text-[10px] font-bold text-[#00529c]">1</span>
                    Transfer tepat <strong class="text-slate-900">{{ $priceLabel }}</strong>
                </li>
                <li class="flex gap-2">
                    <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-[#00529c]/10 text-[10px] font-bold text-[#00529c]">2</span>
                    Cantumkan ID <strong class="font-mono text-slate-800">{{ $orderId }}</strong> di berita transfer / catatan (opsional)
                </li>
                <li class="flex gap-2">
                    <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-[#00529c]/10 text-[10px] font-bold text-[#00529c]">3</span>
                    Isi form di bawah lalu kirim konfirmasi
                </li>
            </ul>
        </div>
    </section>

    {{-- Panduan via bank --}}
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 bg-slate-50 px-4 py-3">
            <h2 class="text-sm font-bold text-slate-900">Panduan transfer bank</h2>
            <p class="mt-0.5 text-xs leading-relaxed text-slate-500">
                Cara melakukan transfer ke rekening Giro BRI <strong class="text-[#00529c]">004101003652303</strong> sebesar <strong>{{ $priceLabel }}</strong> dari berbagai layanan perbankan.
            </p>
        </div>

        <div class="divide-y divide-slate-100">
            @foreach ([
                ['id' => 'bri', 'label' => 'BRI (BRImo)', 'color' => 'text-blue-800', 'bg' => 'bg-blue-50', 'steps' => [
                    'Login ke aplikasi <strong>BRImo</strong>.',
                    'Pilih menu <strong>Transfer</strong> → <strong>Tambah Penerima Baru</strong>.',
                    'Pilih Bank Tujuan: <strong>BRI (Bank Rakyat Indonesia)</strong>.',
                    'Masukkan nomor rekening: <strong>004101003652303</strong>.',
                    'Klik <strong>Lanjutkan</strong> dan pastikan nomor rekening/nama tujuan sudah sesuai.',
                    'Masukkan nominal transfer sebesar <strong>'.$priceLabel.'</strong>.',
                    'Selesaikan transaksi dengan memasukkan PIN BRImo Anda dan simpan bukti transfer.',
                ]],
                ['id' => 'lain', 'label' => 'Bank Lain (BCA, Mandiri, BNI, CIMB, dll)', 'color' => 'text-slate-700', 'bg' => 'bg-slate-50', 'steps' => [
                    'Buka aplikasi mobile banking atau internet banking bank Anda.',
                    'Pilih menu <strong>Transfer Antar Bank</strong> atau <strong>Transfer ke Bank Lain</strong>.',
                    'Pilih bank tujuan <strong>BRI (Bank Rakyat Indonesia)</strong> atau masukkan kode bank <strong>002</strong>.',
                    'Masukkan nomor rekening tujuan: <strong>004101003652303</strong>.',
                    'Masukkan nominal transfer sebesar <strong>'.$priceLabel.'</strong>.',
                    'Periksa nama penerima/rekening tujuan yang muncul agar sesuai.',
                    'Konfirmasi dan selesaikan transaksi dengan PIN Anda, lalu simpan bukti transfer.',
                ]],
            ] as $bank)
                <div>
                    <button
                        type="button"
                        @click="openBank = openBank === '{{ $bank['id'] }}' ? null : '{{ $bank['id'] }}'"
                        class="flex w-full items-center justify-between gap-3 px-4 py-3.5 text-left transition hover:bg-slate-50"
                    >
                        <span class="flex items-center gap-2.5">
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg {{ $bank['bg'] }} text-xs font-bold {{ $bank['color'] }}">
                                {{ strtoupper(substr($bank['id'], 0, 3)) }}
                            </span>
                            <span class="text-sm font-semibold text-slate-900">{{ $bank['label'] }}</span>
                        </span>
                        <svg
                            class="h-4 w-4 shrink-0 text-slate-400 transition"
                            :class="openBank === '{{ $bank['id'] }}' && 'rotate-180'"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        ><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                    </button>
                    <div
                        x-show="openBank === '{{ $bank['id'] }}'"
                        x-cloak
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="border-t border-slate-100 bg-slate-50/50 px-4 py-3"
                    >
                        <ol class="space-y-2 text-xs leading-relaxed text-slate-600">
                            @foreach ($bank['steps'] as $i => $step)
                                <li class="flex gap-2">
                                    <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-white text-[10px] font-bold text-slate-500 ring-1 ring-slate-200">{{ $i + 1 }}</span>
                                    <span>{!! $step !!}</span>
                                </li>
                            @endforeach
                        </ol>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="border-t border-amber-100 bg-amber-50/80 px-4 py-3 text-[11px] leading-relaxed text-amber-900">
            <strong>Tips:</strong> Pastikan nomor rekening
            <button type="button" @click="copy('004101003652303', 'rekening')" class="font-bold text-[#00529c] underline">004101003652303</button>
            dan nominal <strong>{{ $priceLabel }}</strong> sudah benar sebelum konfirmasi transfer.
        </div>
    </section>

    {{-- Detail pesanan --}}
    <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <h2 class="mb-3 text-xs font-bold uppercase tracking-wide text-slate-400">Ringkasan pesanan</h2>
        <dl class="space-y-2.5 text-sm">
            <div class="flex justify-between gap-3">
                <dt class="text-slate-500">Layanan</dt>
                <dd class="text-right font-medium text-slate-900">Chat {{ $provider['specialty'] ?? 'Konsultasi' }}</dd>
            </div>
            <div class="flex justify-between gap-3">
                <dt class="text-slate-500">Tenaga kesehatan</dt>
                <dd class="max-w-[55%] text-right text-xs font-medium leading-snug text-slate-900">{{ $provider['short_name'] ?? $provider['name'] }}</dd>
            </div>
            <div class="flex items-center justify-between gap-3">
                <dt class="text-slate-500">ID transaksi</dt>
                <dd class="flex items-center gap-1.5">
                    <span class="font-mono text-xs text-slate-700">{{ $orderId }}</span>
                    <button type="button" @click="copy('{{ $orderId }}', 'order')" class="rounded-md p-1 text-slate-400 hover:bg-slate-100 hover:text-[#108EE9]" title="Salin ID">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0013.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 01-.75.75H9.75a.75.75 0 01-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 011.927-.184"/></svg>
                    </button>
                </dd>
            </div>
            @if ($hasDiscount)
                <div class="flex justify-between gap-3 border-t border-slate-100 pt-2.5">
                    <dt class="text-slate-500">Harga normal</dt>
                    <dd class="text-slate-500 line-through">{{ $originalPriceLabel }}</dd>
                </div>
                <div class="flex justify-between gap-3 text-emerald-700">
                    <dt>Diskon voucher</dt>
                    <dd>- {{ $discountLabel }}</dd>
                </div>
            @endif
            <div class="flex justify-between gap-3 border-t border-slate-100 pt-2.5">
                <dt class="font-bold text-slate-900">Total transfer</dt>
                <dd class="text-lg font-bold text-[#00529c]">{{ $priceLabel }}</dd>
            </div>
        </dl>
    </section>

    {{-- Form konfirmasi --}}
    <form method="POST" action="{{ route('consultation.pay.dana', $providerKey) }}" enctype="multipart/form-data" class="space-y-3">
        @csrf
        <input type="hidden" name="order_reference" value="{{ $orderId }}">

        <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <label class="mb-1 block text-sm font-bold text-slate-900">Identitas pengirim pembayaran</label>
            <p class="mb-3 text-xs leading-relaxed text-slate-500">
                Isi nama pemilik rekening, nama bank, atau nomor rekening pengirim yang Anda pakai saat transfer — agar admin bisa cocokkan bukti bayar.
            </p>
            <input
                type="text"
                name="dana_phone"
                value="{{ old('dana_phone', auth()->user()->phone) }}"
                placeholder="Contoh: BRI 004101xxxxxx a.n. Nama Pengirim atau BCA 1234567890 a.n. Nama"
                required
                maxlength="120"
                class="w-full rounded-xl border border-slate-200 px-4 py-3.5 text-sm focus:border-[#00529c] focus:outline-none focus:ring-2 focus:ring-[#00529c]/20"
            >
            <p class="mt-2 text-[10px] text-slate-400">Boleh kombinasi bank + nomor rekening + nama pemilik rekening.</p>
            @error('dana_phone')
                <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <label class="mb-1 block text-sm font-bold text-slate-900">Bukti transfer</label>
            <p class="mb-3 text-xs leading-relaxed text-slate-500">
                Upload screenshot atau foto bukti transfer dari aplikasi m-banking, ATM, atau e-wallet. Format JPG, PNG, WEBP, atau PDF — maks. 5 MB.
            </p>
            <label class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed border-slate-200 bg-slate-50/80 px-4 py-6 transition hover:border-[#00529c]/40 hover:bg-[#00529c]/5">
                <svg class="h-8 w-8 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                <span class="text-xs font-semibold text-[#00529c]">Pilih file bukti transfer</span>
                <span class="text-[10px] text-slate-400">Screenshot struk / m-banking / ATM</span>
                <input
                    type="file"
                    name="payment_proof"
                    accept="image/jpeg,image/png,image/webp,application/pdf"
                    required
                    class="sr-only"
                    onchange="this.closest('label').querySelector('[data-filename]').textContent = this.files[0]?.name || 'Pilih file bukti transfer'"
                >
                <span data-filename class="max-w-full truncate text-[10px] text-slate-500">Belum ada file dipilih</span>
            </label>
            @error('payment_proof')
                <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </section>

        <label class="flex items-start gap-3 rounded-2xl border border-amber-100 bg-amber-50/80 p-4 text-xs leading-relaxed text-slate-700">
            <input type="checkbox" name="payment_confirmed" value="1" required class="mt-0.5 rounded border-slate-300 text-[#00529c] focus:ring-[#00529c]/20">
            <span>Saya sudah transfer <strong>{{ $priceLabel }}</strong> ke Giro BRI <strong>004101003652303</strong>, melampirkan bukti transfer, dan siap menunggu verifikasi admin.</span>
        </label>
        @error('payment_confirmed')
            <p class="text-xs text-rose-600">{{ $message }}</p>
        @enderror

        <button
            type="submit"
            class="flex w-full items-center justify-center gap-2 rounded-2xl bg-[#00529c] py-4 text-sm font-bold text-white shadow-lg shadow-[#00529c]/25 transition hover:bg-[#004787] active:scale-[0.98]"
        >
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Kirim konfirmasi pembayaran
        </button>
    </form>

    <div class="rounded-2xl border border-slate-100 bg-slate-50 px-4 py-3 text-center text-[11px] leading-relaxed text-slate-500">
        Setelah konfirmasi, admin akan verifikasi transfer dalam beberapa menit.
        Anda akan diarahkan ke halaman <strong>menunggu verifikasi</strong>, lalu bisa chat WhatsApp live.
    </div>
</div>
@endsection
