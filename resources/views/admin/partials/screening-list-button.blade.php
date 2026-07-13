@props(['session', 'detailUrl'])

@php
    $isInitial = $session->isInitialScreening();
    $theme = $session->riskTheme();
    $subjectName = $session->user?->name ?? ($session->identity?->name ?? 'Tamu');
@endphp

<a
    href="{{ $detailUrl }}"
    @class([
        'flex items-center gap-2.5 rounded-xl border px-3 py-2.5 text-left shadow-sm transition active:scale-[0.99]',
        $isInitial
            ? 'border-brand-200 bg-brand-50/60 hover:bg-brand-50'
            : 'border-slate-200 bg-white hover:bg-slate-50',
    ])
>
    @if ($isInitial)
        <span class="inline-flex shrink-0 items-center rounded-lg bg-brand-600 px-2 py-1 text-[10px] font-bold uppercase tracking-wide text-white">
            Awal
        </span>
        <div class="min-w-0 flex-1">
            <p class="truncate text-xs font-semibold text-slate-900">Skrining Awal</p>
            <p class="truncate text-[10px] text-slate-500">
                {{ $subjectName }}
                · {{ $session->formattedDateTime('d M, H:i') }}
                · {{ $session->historyBadgeLabel() }}
            </p>
        </div>
    @else
        <span class="inline-flex shrink-0 items-center rounded-lg bg-emerald-600 px-2 py-1 text-[10px] font-bold uppercase tracking-wide text-white">
            Penyakit
        </span>
        <div class="min-w-0 flex-1">
            <p class="truncate text-xs font-semibold text-slate-900">{{ $session->diseaseLabel() ?? 'Skrining' }}</p>
            <p class="truncate text-[10px] text-slate-500">
                {{ $subjectName }}
                · {{ $session->formattedDateTime('d M, H:i') }}
                @if ($session->scoreSummary())
                    · {{ $session->scoreSummary() }}
                @endif
            </p>
        </div>
        <span @class([
            'hidden shrink-0 rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide ring-1 sm:inline-flex',
            $theme['bg'], $theme['text'], $theme['ring'],
        ])>
            {{ $session->displayRiskLabel() }}
        </span>
    @endif

    <svg class="h-3.5 w-3.5 shrink-0 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
    </svg>
</a>
