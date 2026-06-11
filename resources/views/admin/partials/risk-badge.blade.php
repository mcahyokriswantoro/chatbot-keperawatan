@props(['session'])

@php
    $theme = $session->riskTheme();
@endphp

<span @class([
    'inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-bold uppercase tracking-wide ring-1',
    $theme['bg'], $theme['text'], $theme['ring'],
])>
    {{ $session->displayRiskLabel() }}
</span>

@if ($session->showsEmergencyUi())
    <span class="ml-1 inline-flex rounded-full bg-rose-100 px-2 py-0.5 text-[10px] font-bold text-rose-700 ring-1 ring-rose-200">
        Darurat
    </span>
@endif
