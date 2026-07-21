@php
    $paymentsActive = request()->routeIs('admin.consultations.index');
    $chatActive = request()->routeIs('admin.consultations.chat.*');
    $vouchersActive = request()->routeIs('admin.consultations.vouchers.*');
    $providersActive = request()->routeIs('admin.consultations.providers.*');
@endphp

@if (auth()->user()?->isAdmin())
<div class="mb-4 flex gap-2 overflow-x-auto pb-1">
    <a
        href="{{ route('admin.consultations.index') }}"
        @class([
            'shrink-0 rounded-xl px-4 py-2.5 text-center text-xs font-bold transition',
            'bg-emerald-600 text-white shadow-sm' => $paymentsActive,
            'border border-slate-200 bg-white text-slate-600' => ! $paymentsActive,
        ])
    >
        Verifikasi bayar
    </a>
    <a
        href="{{ route('admin.consultations.chat.index') }}"
        @class([
            'shrink-0 rounded-xl px-4 py-2.5 text-center text-xs font-bold transition',
            'bg-teal-600 text-white shadow-sm' => $chatActive,
            'border border-slate-200 bg-white text-slate-600' => ! $chatActive,
        ])
    >
        Chat pasien
    </a>
    <a
        href="{{ route('admin.consultations.providers.index') }}"
        @class([
            'shrink-0 rounded-xl px-4 py-2.5 text-center text-xs font-bold transition',
            'bg-sky-600 text-white shadow-sm' => $providersActive,
            'border border-slate-200 bg-white text-slate-600' => ! $providersActive,
        ])
    >
        Tenaga kesehatan
    </a>
    <a
        href="{{ route('admin.consultations.vouchers.index') }}"
        @class([
            'shrink-0 rounded-xl px-4 py-2.5 text-center text-xs font-bold transition',
            'bg-violet-600 text-white shadow-sm' => $vouchersActive,
            'border border-slate-200 bg-white text-slate-600' => ! $vouchersActive,
        ])
    >
        Kelola voucher
    </a>
</div>
@endif
