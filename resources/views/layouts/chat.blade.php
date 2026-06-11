<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0066ff">

    <title>{{ $title ?? 'Deteksi Kesehatan — '.config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">

    <x-app.production-assets />
</head>
<body class="font-sans antialiased bg-brand-50 text-slate-800 overflow-hidden">
    <div class="mx-auto flex h-[100dvh] max-w-md flex-col">
        @yield('content')
    </div>

    @stack('scripts')
</body>
</html>
