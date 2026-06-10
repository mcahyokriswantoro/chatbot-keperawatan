<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php($faviconVersion = filemtime(public_path('favicon.png')) ?: time())
    <title>Admin — {{ config('app.name') }}</title>
    <link rel="icon" href="{{ asset('favicon.png') }}?v={{ $faviconVersion }}" type="image/png" sizes="any">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v={{ $faviconVersion }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-100 text-slate-800">
    <div class="min-h-screen">
        <nav class="bg-slate-900 text-white px-6 py-4 flex items-center justify-between">
            <a href="{{ route('admin.dashboard') }}" class="font-bold">Admin Panel</a>
            <div class="flex gap-4 text-sm">
                <a href="{{ route('admin.articles.index') }}" class="hover:text-brand-300">Artikel</a>
                <a href="{{ route('home') }}" class="hover:text-brand-300">← Situs</a>
            </div>
        </nav>
        <main class="max-w-5xl mx-auto px-6 py-8">
            @if (session('status'))
                <div class="mb-4 rounded-lg bg-emerald-100 border border-emerald-300 px-4 py-3 text-emerald-800 text-sm">{{ session('status') }}</div>
            @endif
            @yield('content')
        </main>
    </div>
</body>
</html>
