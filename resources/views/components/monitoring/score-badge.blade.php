@props(['label'])

@php
    $colors = [
        'baik' => 'bg-emerald-100 text-emerald-800 ring-emerald-200',
        'cukup' => 'bg-amber-100 text-amber-800 ring-amber-200',
        'kurang' => 'bg-rose-100 text-rose-800 ring-rose-200',
    ];
@endphp

@if ($label)
    <span @class(['inline-flex shrink-0 rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide ring-1', $colors[$label] ?? 'bg-slate-100 text-slate-700 ring-slate-200'])>
        {{ config("monitoring.score_labels.{$label}", ucfirst($label)) }}
    </span>
@endif
