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
        $detectionNavUrl = auth()->check() ? route('detection.start') : route('login');
        $selfNavUrl = auth()->check() ? route('self-management') : route('login');
        $desktopNavItems = [
            ['label' => 'Beranda', 'url' => route('home'), 'active' => request()->routeIs('home')],
            ['label' => 'Deteksi Kesehatan', 'url' => $detectionNavUrl, 'active' => request()->routeIs('detection.*')],
            ['label' => 'Self Management', 'url' => $selfNavUrl, 'active' => request()->routeIs('self-management*')],
            ['label' => 'Konsultasi', 'url' => route('consultation.index'), 'active' => request()->routeIs('consultation.*')],
            ['label' => 'Obat & Vitamin', 'url' => route('medicines.index'), 'active' => request()->routeIs('medicines.*')],
            ['label' => 'Homecare', 'url' => route('homecare.index'), 'active' => request()->routeIs('homecare.*')],
            ['label' => 'Edukasi', 'url' => route('education.index'), 'active' => request()->routeIs('education.*')],
        ];
    @endphp

    <title>{{ $title ?? config('app.name', 'Nersia Health') }}</title>

    <link rel="icon" href="{{ asset('favicon.png') }}?v={{ $faviconVersion }}" type="image/png" sizes="any">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v={{ $faviconVersion }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}?v={{ $faviconVersion }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet">

    <x-app.production-assets />

    <style>
        html {
            -webkit-text-size-adjust: 100%;
            overflow-x: hidden;
        }
        body {
            overflow-x: hidden;
            overscroll-behavior-x: none;
            font-family: Figtree, ui-sans-serif, system-ui, sans-serif;
            background: #f8fafc;
            color: #1e293b;
            margin: 0;
        }
        .ck-shell {
            box-sizing: border-box;
            width: 100%;
            margin: 0 auto;
        }
        .ck-shell *, .ck-bottom-nav * {
            box-sizing: border-box;
        }
        .ck-bottom-nav {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 50;
            background: rgba(255, 255, 255, 0.95);
            border-top: 1px solid #d9e8ff;
        }
        @media (min-width: 768px) {
            .ck-bottom-nav {
                position: absolute;
                border-bottom-left-radius: 2.25rem;
                border-bottom-right-radius: 2.25rem;
            }
        }
        .app-scrollbar::-webkit-scrollbar {
            width: 5px;
        }
        .app-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .app-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        .app-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-8px) rotate(2deg); }
        }
    </style>
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-800 overflow-x-hidden min-h-screen flex flex-col selection:bg-[#00529c] selection:text-white">

    {{-- Premium Desktop Header / Navbar --}}
    <header class="hidden md:block sticky top-0 z-50 bg-white/90 backdrop-blur-xl border-b border-slate-200/80 shadow-[0_4px_25px_rgba(0,82,156,0.05)]">
        <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between gap-4">
            {{-- Brand Logo & Title with Robot Icon --}}
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                <div class="relative flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-[#00529c]/10 via-sky-50 to-blue-100/60 p-1.5 ring-1 ring-[#00529c]/20 shadow-xs transition duration-300 group-hover:scale-105 group-hover:shadow-md">
                    <img src="{{ asset('images/robot.png') }}?v={{ filemtime(public_path('images/robot.png')) }}" alt="Nersia Robot" class="h-full w-full object-contain drop-shadow-xs">
                </div>
                <div>
                    <span class="text-base font-black tracking-tight leading-tight block group-hover:opacity-90 transition-opacity">
                        <span class="text-[#002966]">Nersia</span> <span class="text-[#0aa4b0]">Health</span>
                    </span>
                </div>
            </a>

            {{-- Navigation Links --}}
            <nav class="flex items-center gap-1.5 bg-slate-100/70 p-1 rounded-2xl border border-slate-200/60">
                @foreach ($desktopNavItems as $nav)
                    <a
                        href="{{ $nav['url'] }}"
                        @class([
                            'px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all duration-200',
                            'bg-gradient-to-r from-[#00529c] to-[#0066c2] text-white shadow-md shadow-[#00529c]/20 scale-[1.02]' => $nav['active'],
                            'text-slate-600 hover:text-[#00529c] hover:bg-white/80' => ! $nav['active'],
                        ])
                    >
                        {{ $nav['label'] }}
                    </a>
                @endforeach
            </nav>

            {{-- Right User Profile Pill / Login --}}
            <div class="flex items-center gap-2">
                @if (auth()->check())
                    <a href="{{ route('profile.page') }}" @class([
                        'flex items-center gap-2.5 rounded-2xl border px-3.5 py-1.5 transition duration-200 text-xs font-bold shadow-xs',
                        'border-[#00529c] bg-[#00529c]/10 text-[#00529c] ring-2 ring-[#00529c]/20' => request()->routeIs('profile.*'),
                        'border-slate-200 bg-white text-slate-700 hover:border-[#00529c] hover:bg-slate-50' => ! request()->routeIs('profile.*'),
                    ])>
                        <div class="relative flex h-7 w-7 items-center justify-center rounded-xl bg-gradient-to-br from-[#00529c] to-[#00386c] text-white font-extrabold text-xs shadow-xs">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            <span class="absolute -bottom-0.5 -right-0.5 h-2.5 w-2.5 rounded-full bg-emerald-500 ring-2 ring-white"></span>
                        </div>
                        <span class="max-w-[120px] truncate text-slate-800 font-bold">{{ auth()->user()->name }}</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="rounded-2xl bg-gradient-to-r from-[#00529c] to-[#0066c2] px-5 py-2 text-xs font-bold text-white shadow-md shadow-[#00529c]/20 transition hover:opacity-95 active:scale-95">
                        Masuk
                    </a>
                @endif
            </div>
        </div>
    </header>

    {{-- Main Content Container --}}
    <main class="flex-1 w-full min-h-screen md:min-h-0">
        <div class="mx-auto w-full max-w-md md:max-w-5xl lg:max-w-6xl px-4 sm:px-6 md:px-6 pb-32 md:pb-16 pt-5 md:pt-8">
            @yield('content')
        </div>
    </main>

    {{-- Mobile Bottom Navigation (Hidden on desktop >= 768px) --}}
    <div class="md:hidden">
        @include('components.mobile.bottom-nav')
    </div>

    @stack('scripts')
</body>
</html>
