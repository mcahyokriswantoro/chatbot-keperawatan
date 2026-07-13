@props(['order'])

@php
    $providerModel = \App\Models\ConsultationProvider::query()->where('key', $order->provider_key)->first();
    $provider = $providerModel
        ? ['short_name' => $providerModel->short_name, 'name' => $providerModel->name]
        : config("consultation.providers.{$order->provider_key}", []);
    $providerName = $provider['short_name'] ?? $provider['name'] ?? $order->provider_key;
@endphp

<a
    href="{{ route('admin.consultations.index', ['status' => $order->status === 'pending' ? 'pending' : 'all']) }}"
    @class([
        'flex items-center gap-2.5 rounded-xl border px-3 py-2.5 text-left shadow-sm transition active:scale-[0.99]',
        'border-amber-200 bg-amber-50/60 hover:bg-amber-50' => $order->status === 'pending',
        'border-emerald-200 bg-emerald-50/60 hover:bg-emerald-50' => $order->status === 'paid',
        'border-rose-200 bg-rose-50/60 hover:bg-rose-50' => $order->status === 'rejected',
    ])
>
    <span @class([
        'inline-flex shrink-0 items-center rounded-lg px-2 py-1 text-[10px] font-bold uppercase tracking-wide text-white',
        'bg-amber-500' => $order->status === 'pending',
        'bg-emerald-600' => $order->status === 'paid',
        'bg-rose-500' => $order->status === 'rejected',
    ])>
        {{ $order->status === 'pending' ? 'Pending' : ($order->status === 'paid' ? 'Lunas' : 'Tolak') }}
    </span>

    <div class="min-w-0 flex-1">
        <p class="truncate text-xs font-semibold text-slate-900">
            {{ $order->user?->name ?? 'Pengguna' }} · {{ 'Rp '.number_format($order->total_paid, 0, ',', '.') }}
        </p>
        <p class="truncate text-[10px] text-slate-500">
            {{ $providerName }}
            · {{ $order->reference_code ?? '—' }}
            · {{ $order->created_at?->timezone(config('app.timezone'))->format('d M, H:i') }}
        </p>
    </div>

    <svg class="h-3.5 w-3.5 shrink-0 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
    </svg>
</a>
