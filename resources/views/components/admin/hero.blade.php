@props([
    'name' => auth()->user()->name,
])

@php
    $firstName = explode(' ', trim($name))[0];
    $hour = (int) now()->format('H');
    $greeting = $hour < 11 ? 'Selamat pagi' : ($hour < 15 ? 'Selamat siang' : ($hour < 18 ? 'Selamat sore' : 'Selamat malam'));
    $robotVersion = filemtime(public_path('images/robot.png')) ?: time();
@endphp

<header {{ $attributes->merge(['class' => 'relative mb-5 overflow-hidden rounded-3xl bg-gradient-to-br from-slate-800 via-slate-900 to-brand-900 px-4 pb-16 pt-4 text-white shadow-lg ring-1 ring-brand-900/20']) }}>
    <div class="pointer-events-none absolute -right-8 top-0 h-36 w-36 rounded-full bg-brand-500/25 blur-2xl"></div>
    <div class="pointer-events-none absolute -left-6 bottom-4 h-24 w-24 rounded-full bg-teal-400/15 blur-xl"></div>

    <div class="relative flex items-start gap-3">
        <div class="flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-white/10 ring-1 ring-white/20 backdrop-blur-sm">
            <x-app.medical-note-icon class="h-8 w-8 brightness-0 invert opacity-90" />
        </div>

        <div class="min-w-0 flex-1 pt-0.5">
            <p class="text-[10px] font-bold uppercase tracking-wider text-brand-300">Panel Admin</p>
            <p class="text-xs font-medium text-slate-400">{{ now()->translatedFormat('l, d F Y') }}</p>
            <h1 class="mt-0.5 text-lg font-bold leading-snug">{{ $greeting }}, {{ $firstName }} 👋</h1>
            <p class="mt-1 text-[11px] leading-relaxed text-slate-400">
                Pantau pengguna, skrining, dan monitoring kesehatan
            </p>
        </div>

        <div class="relative h-[4.5rem] w-[4.5rem] shrink-0 animate-[float_3s_ease-in-out_infinite]">
            <img
                src="{{ asset('images/robot.png') }}?v={{ $robotVersion }}"
                alt=""
                class="h-full w-full object-contain drop-shadow-lg"
            />
        </div>
    </div>

    <div class="relative mt-4 flex flex-wrap gap-2">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1.5 text-[11px] font-semibold text-white ring-1 ring-white/20 backdrop-blur-sm transition hover:bg-white/20">
            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75"/></svg>
            Situs
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1.5 text-[11px] font-semibold text-white ring-1 ring-white/20 backdrop-blur-sm transition hover:bg-rose-500/30">
                Keluar
            </button>
        </form>
    </div>
</header>

@once
    @push('scripts')
        <style>
            @keyframes float {
                0%, 100% { transform: translateY(0); }
                50% { transform: translateY(-6px); }
            }
        </style>
    @endpush
@endonce
