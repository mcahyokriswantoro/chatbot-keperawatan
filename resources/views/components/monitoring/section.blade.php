@props(['letter', 'title', 'subtitle' => null, 'accent' => 'brand'])

@php
    $accents = [
        'brand' => 'bg-brand-600 text-white',
        'emerald' => 'bg-emerald-600 text-white',
        'violet' => 'bg-violet-600 text-white',
        'sky' => 'bg-sky-600 text-white',
        'amber' => 'bg-amber-500 text-white',
    ];
@endphp

<section {{ $attributes->merge(['class' => 'overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-card']) }}>
    <div class="flex items-start gap-3 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white px-4 py-3.5">
        <span @class(['flex h-8 w-8 shrink-0 items-center justify-center rounded-xl text-xs font-bold shadow-sm', $accents[$accent] ?? $accents['brand']])>
            {{ $letter }}
        </span>
        <div class="min-w-0 flex-1">
            <h2 class="text-sm font-bold leading-snug text-slate-900">{{ $title }}</h2>
            @if ($subtitle)
                <p class="mt-0.5 text-[11px] leading-relaxed text-slate-500">{{ $subtitle }}</p>
            @endif
        </div>
    </div>
    <div class="p-4">
        {{ $slot }}
    </div>
</section>
