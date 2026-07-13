@extends('layouts.mobile')

@section('content')
@php
    $photoUrl = $provider['photo'] ?? asset('images/avatars/male.svg');
    $hasDiscount = ! empty($voucherCode) && ($dueAmount ?? $price) < $price;
@endphp

<div
    x-data="{
        copied: '',
        openBank: 'bca',
        copy(text, key) {
            navigator.clipboard.writeText(text).then(() => {
                this.copied = key;
                setTimeout(() => this.copied = '', 2000);
            });
        },
    }"
    class="space-y-4 pb-6"
>
    {{-- Header DANA --}}
    <header class="relative -mx-4 overflow-hidden bg-gradient-to-br from-[#108EE9] via-[#0d7fd4] to-[#0066cc] px-5 pb-5 pt-2 text-white shadow-lg sm:mx-0 sm:rounded-3xl">
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
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-lg font-black text-[#108EE9]">D</span>
                <div class="min-w-0">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-blue-100">Pembayaran DANA</p>
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
        <div class="rounded-xl border border-[#108EE9]/30 bg-[#108EE9]/5 px-2 py-2.5 text-center">
            <p class="text-[10px] font-bold text-[#108EE9]">1</p>
            <p class="mt-0.5 text-[9px] font-semibold leading-tight text-slate-700">Transfer</p>
        </div>
        <div class="rounded-xl border border-[#108EE9]/30 bg-[#108EE9]/10 px-2 py-2.5 text-center ring-2 ring-[#108EE9]/20">
            <p class="text-[10px] font-bold text-[#108EE9]">2</p>
            <p class="mt-0.5 text-[9px] font-semibold leading-tight text-slate-700">Konfirmasi</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-slate-50 px-2 py-2.5 text-center">
            <p class="text-[10px] font-bold text-slate-400">3</p>
            <p class="mt-0.5 text-[9px] font-semibold leading-tight text-slate-500">Verifikasi</p>
        </div>
    </div>

    {{-- Nomor DANA tujuan --}}
    <section class="overflow-hidden rounded-2xl border border-[#108EE9]/25 bg-gradient-to-b from-[#108EE9]/5 to-white shadow-sm">
        <div class="border-b border-[#108EE9]/10 px-4 py-3">
            <h2 class="text-sm font-bold text-slate-900">Transfer ke DANA</h2>
            <p class="mt-0.5 text-xs text-slate-500">Buka app DANA → Kirim → masukkan nomor di bawah</p>
        </div>
        <div class="p-4">
            <div class="rounded-2xl border-2 border-dashed border-[#108EE9]/30 bg-white px-4 py-4 text-center">
                <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Nomor DANA tujuan</p>
                <p class="mt-1 text-2xl font-bold tracking-wide text-[#108EE9]">{{ $merchantPhone }}</p>
                <p class="mt-0.5 text-xs text-slate-500">{{ $merchantName }}</p>
                <button
                    type="button"
                    @click="copy('{{ $merchantPhone }}', 'dana')"
                    class="mt-3 inline-flex items-center gap-1.5 rounded-full border border-[#108EE9]/30 bg-[#108EE9]/5 px-4 py-1.5 text-[11px] font-semibold text-[#108EE9] transition hover:bg-[#108EE9]/10"
                >
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0013.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 01-.75.75H9.75a.75.75 0 01-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 011.927-.184"/></svg>
                    <span x-text="copied === 'dana' ? 'Tersalin!' : 'Salin nomor'"></span>
                </button>
            </div>

            <ul class="mt-4 space-y-2 text-xs leading-relaxed text-slate-600">
                <li class="flex gap-2">
                    <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-[#108EE9]/10 text-[10px] font-bold text-[#108EE9]">1</span>
                    Transfer tepat <strong class="text-slate-900">{{ $priceLabel }}</strong>
                </li>
                <li class="flex gap-2">
                    <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-[#108EE9]/10 text-[10px] font-bold text-[#108EE9]">2</span>
                    Cantumkan ID <strong class="font-mono text-slate-800">{{ $orderId }}</strong> di catatan (opsional)
                </li>
                <li class="flex gap-2">
                    <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-[#108EE9]/10 text-[10px] font-bold text-[#108EE9]">3</span>
                    Isi form di bawah lalu kirim konfirmasi
                </li>
            </ul>
        </div>
    </section>

    {{-- Panduan via bank --}}
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 bg-slate-50 px-4 py-3">
            <h2 class="text-sm font-bold text-slate-900">Panduan bayar via bank</h2>
            <p class="mt-0.5 text-xs leading-relaxed text-slate-500">
                Tidak punya saldo DANA? Transfer dari <strong>BCA, BRI, BNI, Mandiri</strong>, atau bank lain ke nomor DANA
                <strong class="text-[#108EE9]">{{ $merchantPhone }}</strong> sebesar <strong>{{ $priceLabel }}</strong>.
            </p>
        </div>

        <div class="divide-y divide-slate-100">
            @foreach ([
                ['id' => 'bca', 'label' => 'BCA (m-BCA / myBCA)', 'color' => 'text-blue-700', 'bg' => 'bg-blue-50', 'steps' => [
                    'Login aplikasi BCA mobile.',
                    'Pilih menu <strong>Transfer</strong> → <strong>Virtual Account</strong> atau <strong>E-Wallet / Dompet Digital</strong>.',
                    'Pilih tujuan <strong>DANA</strong> (jika tersedia), atau <strong>Top Up DANA</strong> ke nomor HP.',
                    'Masukkan nomor DANA tujuan: <strong>'.$merchantPhone.'</strong>.',
                    'Isi nominal <strong>'.$priceLabel.'</strong>, lalu konfirmasi PIN.',
                    'Setelah sukses, kembali ke halaman ini dan kirim konfirmasi pembayaran.',
                ]],
                ['id' => 'bri', 'label' => 'BRI (BRImo)', 'color' => 'text-blue-800', 'bg' => 'bg-blue-50', 'steps' => [
                    'Login aplikasi BRImo.',
                    'Pilih <strong>Transfer</strong> → <strong>E-Wallet</strong> atau <strong>Dompet Digital</strong>.',
                    'Pilih <strong>DANA</strong>.',
                    'Masukkan nomor tujuan <strong>'.$merchantPhone.'</strong> dan nominal <strong>'.$priceLabel.'</strong>.',
                    'Konfirmasi transfer dengan PIN BRImo.',
                    'Kembali ke halaman ini → isi form konfirmasi di bawah.',
                ]],
                ['id' => 'bni', 'label' => 'BNI (BNI Mobile)', 'color' => 'text-orange-700', 'bg' => 'bg-orange-50', 'steps' => [
                    'Login BNI Mobile Banking.',
                    'Pilih <strong>Transfer</strong> → <strong>E-Wallet</strong> / <strong>Dompet Digital</strong>.',
                    'Pilih provider <strong>DANA</strong>.',
                    'Input nomor HP <strong>'.$merchantPhone.'</strong>, nominal <strong>'.$priceLabel.'</strong>.',
                    'Selesaikan dengan PIN / OTP.',
                    'Lanjut konfirmasi pembayaran di form bawah halaman ini.',
                ]],
                ['id' => 'mandiri', 'label' => 'Mandiri (Livin\')', 'color' => 'text-yellow-700', 'bg' => 'bg-yellow-50', 'steps' => [
                    'Login aplikasi Livin\' by Mandiri.',
                    'Pilih <strong>Bayar / Transfer</strong> → <strong>E-Money / E-Wallet</strong>.',
                    'Pilih <strong>DANA</strong>.',
                    'Masukkan nomor <strong>'.$merchantPhone.'</strong> dan nominal <strong>'.$priceLabel.'</strong>.',
                    'Konfirmasi transaksi.',
                    'Setelah berhasil, kirim konfirmasi di form bawah.',
                ]],
                ['id' => 'lain', 'label' => 'Bank lain (CIMB, Permata, dll.)', 'color' => 'text-slate-700', 'bg' => 'bg-slate-50', 'steps' => [
                    'Buka mobile banking bank Anda.',
                    'Cari menu <strong>Transfer ke E-Wallet</strong>, <strong>Dompet Digital</strong>, atau <strong>Top Up E-Money</strong>.',
                    'Pilih <strong>DANA</strong> sebagai tujuan.',
                    'Masukkan nomor DANA <strong>'.$merchantPhone.'</strong>, nominal <strong>'.$priceLabel.'</strong>.',
                    'Jika menu DANA tidak ada: top up saldo DANA Anda dulu dari bank, lalu <strong>Kirim</strong> dari app DANA ke nomor yang sama.',
                    'Kembali ke halaman ini dan tekan <strong>Kirim konfirmasi pembayaran</strong>.',
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
            <strong>Tips:</strong> Nama penerima di bank/e-wallet bisa tampil sebagai nomor HP atau merchant DANA — pastikan nomor tujuan
            <button type="button" @click="copy('{{ $merchantPhone }}', 'dana')" class="font-bold text-[#108EE9] underline">{{ $merchantPhone }}</button>
            dan nominal <strong>{{ $priceLabel }}</strong> sudah benar sebelum konfirmasi.
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
                <dd class="text-lg font-bold text-[#108EE9]">{{ $priceLabel }}</dd>
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
                Isi nomor <strong>DANA</strong>, <strong>rekening bank</strong> (BCA/BRI/BNI/Mandiri/dll.), atau <strong>nomor HP</strong> yang Anda pakai saat transfer — agar admin bisa cocokkan bukti bayar.
            </p>
            <input
                type="text"
                name="dana_phone"
                value="{{ old('dana_phone', auth()->user()->phone) }}"
                placeholder="Contoh: 0856xxx · BCA 1234567890 · BRI 9876543210 a.n. Nama"
                required
                maxlength="120"
                class="w-full rounded-xl border border-slate-200 px-4 py-3.5 text-sm focus:border-[#108EE9] focus:outline-none focus:ring-2 focus:ring-[#108EE9]/20"
            >
            <p class="mt-2 text-[10px] text-slate-400">Boleh kombinasi bank + nomor rekening + nama pemilik rekening.</p>
            @error('dana_phone')
                <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <label class="mb-1 block text-sm font-bold text-slate-900">Bukti transfer</label>
            <p class="mb-3 text-xs leading-relaxed text-slate-500">
                Upload screenshot atau foto bukti transfer dari aplikasi DANA, m-banking, atau ATM. Format JPG, PNG, WEBP, atau PDF — maks. 5 MB.
            </p>
            <label class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed border-slate-200 bg-slate-50/80 px-4 py-6 transition hover:border-[#108EE9]/40 hover:bg-[#108EE9]/5">
                <svg class="h-8 w-8 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                <span class="text-xs font-semibold text-[#108EE9]">Pilih file bukti transfer</span>
                <span class="text-[10px] text-slate-400">Screenshot struk / m-banking / DANA</span>
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
            <input type="checkbox" name="payment_confirmed" value="1" required class="mt-0.5 rounded border-slate-300 text-[#108EE9] focus:ring-[#108EE9]/20">
            <span>Saya sudah transfer <strong>{{ $priceLabel }}</strong> ke DANA <strong>{{ $merchantPhone }}</strong>, melampirkan bukti transfer, dan siap menunggu verifikasi admin.</span>
        </label>
        @error('payment_confirmed')
            <p class="text-xs text-rose-600">{{ $message }}</p>
        @enderror

        <button
            type="submit"
            class="flex w-full items-center justify-center gap-2 rounded-2xl bg-[#108EE9] py-4 text-sm font-bold text-white shadow-lg shadow-[#108EE9]/25 transition hover:bg-[#0e7ed0] active:scale-[0.98]"
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
