@props(['title', 'subtitle'])

<header {{ $attributes->merge(['class' => 'mb-6']) }}>
    <div class="flex items-start gap-4">
        <div class="relative flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-white shadow-lg shadow-brand-600/20 ring-4 ring-brand-100">
            <img src="{{ asset('images/robot.png') }}" alt="" class="h-full w-full object-cover" />
            <span class="absolute -bottom-1 -right-1 flex h-7 w-7 items-center justify-center rounded-full bg-white text-sm shadow-md ring-2 ring-brand-100" aria-hidden="true">🩺</span>
        </div>

        <div class="min-w-0 flex-1 pt-1">
            <p class="text-xs font-semibold uppercase tracking-wide text-brand-600">Nersia Health</p>
            <h1 class="mt-0.5 text-xl font-bold leading-snug text-slate-900">{{ $title }}</h1>
            <p class="mt-1 text-sm leading-relaxed text-slate-500">{{ $subtitle }}</p>
        </div>
    </div>
</header>
