@props(['session', 'detailUrl'])

@php($theme = $session->riskTheme())

<a href="{{ $detailUrl }}" class="block overflow-hidden rounded-2xl border bg-white shadow-sm transition active:scale-[0.99] {{ $theme['border'] }}">
    <div class="flex">
        <div @class(['w-1.5 shrink-0', $theme['accent']])></div>
        <div class="min-w-0 flex-1 p-4">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <p class="font-bold text-slate-900">{{ $session->diseaseLabel() ?? 'Skrining' }}</p>
                    <p class="mt-0.5 text-[11px] text-slate-400">{{ $session->formattedDateTime() }}</p>
                    <p class="mt-1 text-xs text-slate-600">{{ $session->user?->name ?? ($session->identity?->name ?? 'Tamu') }}</p>
                </div>
                @include('admin.partials.risk-badge', ['session' => $session])
            </div>
            @if ($session->scoreSummary())
                <p class="mt-2 text-xs font-medium text-slate-600">{{ $session->scoreSummary() }}</p>
            @endif
        </div>
    </div>
</a>
