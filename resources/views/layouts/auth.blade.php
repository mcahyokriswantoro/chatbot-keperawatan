<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#eef5ff">

    @php($faviconVersion = filemtime(public_path('favicon.png')) ?: time())

    <title>{{ $title ?? 'Masuk — '.config('app.name', 'Chatbot Keperawatan') }}</title>

    <link rel="icon" href="{{ asset('favicon.png') }}?v={{ $faviconVersion }}" type="image/png" sizes="any">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v={{ $faviconVersion }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}?v={{ $faviconVersion }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">

    <x-app.production-assets />
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-800">
    <div class="min-h-screen flex flex-col">
        <div class="mx-auto w-full max-w-md flex-1 flex flex-col px-5 pt-8 pb-10">
            @yield('content')
        </div>
    </div>

    @stack('scripts')
</body>
</html>
