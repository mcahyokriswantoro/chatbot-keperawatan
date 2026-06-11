@props([
    'url',
    'label',
    'sub',
    'bg' => 'from-brand-600 to-brand-500',
    'icon',
])

<a
    href="{{ $url }}"
    {{ $attributes->merge(['class' => "group relative overflow-hidden rounded-2xl bg-gradient-to-br {$bg} p-4 text-white shadow-md transition active:scale-[0.97] hover:shadow-lg"]) }}
>
    <div class="pointer-events-none absolute -right-3 -top-3 h-16 w-16 rounded-full bg-white/10"></div>
    <svg class="relative h-6 w-6 opacity-90" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/>
    </svg>
    <p class="relative mt-3 text-sm font-bold">{{ $label }}</p>
    <p class="relative text-[10px] text-white/75">{{ $sub }}</p>
</a>
