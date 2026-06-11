@props([
    'title',
    'subtitle' => null,
    'back' => null,
    'tone' => 'brand',
    'showActions' => true,
])

@php
    $robotVersion = filemtime(public_path('images/robot.png')) ?: time();
    $gradients = [
        'brand' => 'from-brand-50 via-white to-brand-100/80 ring-brand-100/60',
        'emerald' => 'from-emerald-50 via-white to-teal-50 ring-emerald-100/60',
        'violet' => 'from-violet-50 via-white to-purple-50 ring-violet-100/60',
        'rose' => 'from-rose-50 via-white to-orange-50 ring-rose-100/60',
    ];
    $gradient = $gradients[$tone] ?? $gradients['brand'];
@endphp

<header {{ $attributes->merge(['class' => "relative mb-5 overflow-hidden rounded-3xl bg-gradient-to-br {$gradient} px-4 py-4 shadow-soft ring-1"]) }}>
    <div class="flex items-start gap-3">
        @if ($back)
            <a href="{{ $back }}" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white text-brand-600 shadow-sm ring-1 ring-brand-100" aria-label="Kembali">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
            </a>
        @else
            <div class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-white shadow-sm ring-2 ring-brand-100">
                <x-app.medical-note-icon class="h-7 w-7" />
            </div>
        @endif

        <div class="min-w-0 flex-1 pt-0.5">
            <p class="text-[10px] font-bold uppercase tracking-wide text-brand-600">Admin</p>
            <h1 class="text-lg font-bold leading-tight text-slate-900">{{ $title }}</h1>
            @if ($subtitle)
                <p class="mt-0.5 text-[11px] leading-relaxed text-slate-500">{{ $subtitle }}</p>
            @endif
        </div>

        <div class="relative h-14 w-14 shrink-0">
            <img
                src="{{ asset('images/robot.png') }}?v={{ $robotVersion }}"
                alt=""
                class="h-full w-full object-contain drop-shadow-md"
            />
            <span class="absolute -bottom-0.5 -right-0.5 flex h-5 w-5 items-center justify-center rounded-full bg-white text-[10px] shadow ring-1 ring-brand-100" aria-hidden="true">📊</span>
        </div>
    </div>

    @if ($showActions ?? true)
        <div class="mt-3 flex gap-2">
            <a href="{{ route('home') }}" class="rounded-full bg-white/80 px-3 py-1 text-[10px] font-semibold text-brand-700 ring-1 ring-brand-100">← Situs</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="rounded-full bg-white/80 px-3 py-1 text-[10px] font-semibold text-slate-600 ring-1 ring-slate-200">Keluar</button>
            </form>
        </div>
    @endif
</header>
