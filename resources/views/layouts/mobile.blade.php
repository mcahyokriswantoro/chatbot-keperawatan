<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="screening-tts-client" content="{{ config('screening_tts.client_only', true) ? '1' : '0' }}">
    <meta name="theme-color" content="#eef5ff">

    @php($faviconVersion = filemtime(public_path('favicon.png')) ?: time())

    <title>{{ $title ?? config('app.name', 'Chatbot Keperawatan') }}</title>

    <link rel="icon" href="{{ asset('favicon.png') }}?v={{ $faviconVersion }}" type="image/png" sizes="any">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v={{ $faviconVersion }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}?v={{ $faviconVersion }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">

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
            max-width: 28rem;
            margin: 0 auto;
            padding: 1.25rem 1rem 8rem;
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
    </style>
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-800 overflow-x-hidden">
    <div class="min-h-screen flex flex-col overflow-x-hidden">
        <div class="ck-shell mx-auto w-full max-w-md flex-1 flex flex-col px-4 pb-32 pt-5 sm:px-5 sm:pb-[calc(6.5rem+env(safe-area-inset-bottom,0px))] sm:pt-[max(1.25rem,env(safe-area-inset-top,0px))]">
            @yield('content')
        </div>
    </div>

    @include('components.mobile.bottom-nav')

    @stack('scripts')
</body>
</html>
