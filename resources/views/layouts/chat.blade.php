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
<body class="font-sans antialiased bg-slate-50 text-slate-800 overflow-x-hidden min-h-screen flex flex-col selection:bg-[#00529c] selection:text-white">
    <div class="mx-auto flex h-[100dvh] md:h-auto md:min-h-screen w-full max-w-md md:max-w-5xl lg:max-w-6xl flex-col bg-white md:bg-transparent md:p-6 lg:p-8">
        @yield('content')
    </div>

    @stack('scripts')
</body>
</html>
