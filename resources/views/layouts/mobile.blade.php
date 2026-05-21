<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#eef5ff">

    <title>{{ $title ?? config('app.name', 'ChatSimpel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gradient-to-b from-brand-50 via-white to-brand-50 text-slate-800">
    <div class="min-h-screen flex flex-col">
        <div class="mx-auto w-full max-w-md flex-1 flex flex-col px-5 pt-6 pb-28">
            @yield('content')
        </div>
    </div>

    @include('components.mobile.bottom-nav')

    @stack('scripts')
</body>
</html>
