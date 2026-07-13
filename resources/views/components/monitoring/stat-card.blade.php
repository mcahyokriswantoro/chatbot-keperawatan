@props(['label', 'value', 'badge' => null])

<div class="flex items-center justify-between gap-3 rounded-xl bg-slate-50 px-3 py-2.5 ring-1 ring-slate-100">
    <div class="min-w-0">
        <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">{{ $label }}</p>
        <p class="mt-0.5 text-sm font-bold text-slate-900">{{ $value }}</p>
    </div>
    @if ($badge)
        <x-monitoring.score-badge :label="$badge" />
    @endif
</div>
