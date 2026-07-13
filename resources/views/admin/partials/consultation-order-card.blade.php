@props(['order', 'showActions' => true])

@php
    $providerModel = \App\Models\ConsultationProvider::query()->where('key', $order->provider_key)->first();
    $provider = $providerModel
        ? ['short_name' => $providerModel->short_name, 'name' => $providerModel->name]
        : config("consultation.providers.{$order->provider_key}", []);
    $providerName = $provider['short_name'] ?? $provider['name'] ?? $order->provider_key;
    $statusLabel = match ($order->status) {
        'pending' => 'Menunggu',
        'paid' => 'Disetujui',
        'rejected' => 'Ditolak',
        default => $order->status,
    };
@endphp

<article {{ $attributes->merge(['class' => 'rounded-2xl border border-slate-200 bg-white p-4 shadow-sm']) }}>
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <p class="text-sm font-bold text-slate-900">{{ $order->user?->name ?? '—' }}</p>
            <p class="text-xs text-slate-500">{{ $order->user?->email }}</p>
        </div>
        <span @class([
            'shrink-0 rounded-full px-2.5 py-0.5 text-[10px] font-bold uppercase',
            'bg-amber-100 text-amber-800' => $order->status === 'pending',
            'bg-emerald-100 text-emerald-800' => $order->status === 'paid',
            'bg-rose-100 text-rose-800' => $order->status === 'rejected',
        ])>
            {{ $statusLabel }}
        </span>
    </div>

    <dl class="mt-3 grid gap-2 text-xs">
        <div class="flex justify-between gap-3">
            <dt class="text-slate-500">Perawat</dt>
            <dd class="font-medium text-slate-900">{{ $providerName }}</dd>
        </div>
        <div class="flex justify-between gap-3">
            <dt class="text-slate-500">Nominal</dt>
            <dd class="font-bold text-slate-900">{{ 'Rp '.number_format($order->total_paid, 0, ',', '.') }}</dd>
        </div>
        <div class="flex justify-between gap-3">
            <dt class="text-slate-500">ID transaksi</dt>
            <dd class="font-mono text-slate-700">{{ $order->reference_code ?? '—' }}</dd>
        </div>
            <div class="flex justify-between gap-3">
                <dt class="text-slate-500">Identitas pengirim</dt>
                <dd class="max-w-[55%] text-right text-xs font-medium leading-snug text-slate-900">{{ $order->dana_phone ?? '—' }}</dd>
            </div>
        @if ($proofUrl = $order->paymentProofUrl())
            <div class="mt-1">
                <dt class="mb-2 text-slate-500">Bukti transfer</dt>
                <dd>
                    @if ($order->paymentProofIsImage())
                        <a href="{{ $proofUrl }}" target="_blank" rel="noopener" class="block overflow-hidden rounded-xl border border-slate-200 bg-slate-50">
                            <img src="{{ $proofUrl }}" alt="Bukti transfer" class="max-h-48 w-full object-contain">
                        </a>
                    @else
                        <a href="{{ $proofUrl }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-200">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                            Lihat bukti PDF
                        </a>
                    @endif
                </dd>
            </div>
        @endif
        <div class="flex justify-between gap-3">
            <dt class="text-slate-500">DANA tujuan</dt>
            <dd class="font-medium text-slate-900">{{ config('consultation.dana.merchant_phone', '085645527751') }}</dd>
        </div>
        <div class="flex justify-between gap-3">
            <dt class="text-slate-500">Waktu</dt>
            <dd class="text-slate-700">{{ $order->created_at?->timezone(config('app.timezone'))->format('d M Y, H:i') }}</dd>
        </div>
        @if ($order->admin_note)
            <div class="rounded-xl bg-slate-50 px-3 py-2 text-slate-600">
                {{ $order->admin_note }}
            </div>
        @endif
    </dl>

    @if ($showActions && $order->status === 'pending')
        <div class="mt-4 flex gap-2">
            <form method="POST" action="{{ route('admin.consultations.approve', $order) }}" class="flex-1">
                @csrf
                <button type="submit" class="w-full rounded-xl bg-emerald-600 py-2.5 text-xs font-bold text-white hover:bg-emerald-700">
                    Setujui → aktifkan chat
                </button>
            </form>
            <form method="POST" action="{{ route('admin.consultations.reject', $order) }}" class="flex-1">
                @csrf
                <input type="hidden" name="admin_note" value="Transfer tidak ditemukan atau nominal tidak sesuai.">
                <button type="submit" class="w-full rounded-xl border border-rose-200 bg-rose-50 py-2.5 text-xs font-bold text-rose-700 hover:bg-rose-100">
                    Tolak
                </button>
            </form>
        </div>
    @endif
</article>
