<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="screening-tts-client" content="{{ config('screening_tts.client_only', true) ? '1' : '0' }}">
    <meta name="theme-color" content="#eef5ff">
    @php
        $faviconVersion = filemtime(public_path('favicon.png')) ?: time();
        $adminNavItems = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard'), 'active' => request()->routeIs('admin.dashboard')],
            ['label' => 'Hasil Skrining', 'url' => route('admin.screenings.index'), 'active' => request()->routeIs('admin.screenings.*')],
            ['label' => 'Data Pasien', 'url' => route('admin.users.index'), 'active' => request()->routeIs('admin.users.*')],
            ['label' => 'Konsultasi', 'url' => route('admin.consultations.index'), 'active' => request()->routeIs('admin.consultations.*')],
            ['label' => 'Kelola Obat', 'url' => route('admin.medicines.index'), 'active' => request()->routeIs('admin.medicines.*')],
            ['label' => 'Homecare', 'url' => route('admin.homecare.index'), 'active' => request()->routeIs('admin.homecare.*')],
            ['label' => 'Edukasi', 'url' => route('admin.articles.index'), 'active' => request()->routeIs('admin.articles.*')],
        ];
    @endphp
    <title>@yield('title', 'Admin') — {{ config('app.name') }}</title>
    <link rel="icon" href="{{ asset('favicon.png') }}?v={{ $faviconVersion }}" type="image/png" sizes="any">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v={{ $faviconVersion }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}?v={{ $faviconVersion }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet">
    <x-app.production-assets />
    @php($adminChartsCss = filemtime(public_path('css/admin-charts.css')) ?: time())
    <link rel="stylesheet" href="/css/admin-charts.css?v={{ $adminChartsCss }}">
    @stack('styles')
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-8px) rotate(2deg); }
        }
    </style>
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-800 overflow-x-hidden min-h-screen flex flex-col selection:bg-[#00529c] selection:text-white">

    {{-- Premium Desktop Header / Navbar for Admin --}}
    <header class="hidden md:block sticky top-0 z-50 bg-white/90 backdrop-blur-xl border-b border-slate-200/80 shadow-[0_4px_25px_rgba(0,82,156,0.05)]">
        <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between gap-4">
            {{-- Brand Logo & Admin Badge --}}
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 group">
                <div class="relative flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-[#00529c]/10 via-sky-50 to-blue-100/60 p-1.5 ring-1 ring-[#00529c]/20 shadow-xs transition duration-300 group-hover:scale-105 group-hover:shadow-md">
                    <img src="{{ asset('images/robot.png') }}?v={{ filemtime(public_path('images/robot.png')) }}" alt="Nersia Robot" class="h-full w-full object-contain drop-shadow-xs">
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-base font-black tracking-tight leading-tight block group-hover:opacity-90 transition-opacity">
                            <span class="text-[#002966]">Nersia</span> <span class="text-[#0aa4b0]">Health</span>
                        </span>
                        <span class="rounded-full bg-slate-900 px-2 py-0.5 text-[10px] font-extrabold text-amber-400 uppercase tracking-wider">Admin</span>
                    </div>
                </div>
            </a>

            {{-- Navigation Links --}}
            <nav class="flex items-center gap-1 bg-slate-100/70 p-1 rounded-2xl border border-slate-200/60">
                @foreach ($adminNavItems as $nav)
                    <a
                        href="{{ $nav['url'] }}"
                        @class([
                            'px-3 py-1.5 rounded-xl text-xs font-bold transition-all duration-200',
                            'bg-gradient-to-r from-[#00529c] to-[#0066c2] text-white shadow-md shadow-[#00529c]/20 scale-[1.02]' => $nav['active'],
                            'text-slate-600 hover:text-[#00529c] hover:bg-white/80' => ! $nav['active'],
                        ])
                    >
                        {{ $nav['label'] }}
                    </a>
                @endforeach
            </nav>

            {{-- Right Button: Back to Main Web App --}}
            <div class="flex items-center gap-2">
                <a href="{{ route('home') }}" class="flex items-center gap-1.5 rounded-2xl border border-slate-200 bg-white px-3.5 py-1.5 text-xs font-bold text-slate-700 hover:border-[#00529c] hover:text-[#00529c] hover:bg-slate-50 transition shadow-xs">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                    Aplikasi Web
                </a>
            </div>
        </div>
    </header>

    <div class="min-h-screen flex flex-col flex-1">
        <div class="mx-auto w-full max-w-md md:max-w-6xl flex-1 flex flex-col px-4 sm:px-6 md:px-6 pt-5 md:pt-8 pb-28 md:pb-16">
            @if (session('status'))
                <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    {{-- Mobile Bottom Navigation --}}
    <div class="md:hidden">
        @include('components.admin.bottom-nav')
    </div>

    @stack('scripts')
</body>
</html>
