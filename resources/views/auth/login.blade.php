@extends('layouts.mobile')

@section('content')
    <header class="mb-6 flex items-start gap-4">
        <x-mobile.logo />

        <div class="flex-1 pt-1">
            <h1 class="text-xl font-bold leading-snug text-slate-900">
                Masuk ke Akun
            </h1>
            <p class="mt-1 text-sm font-medium text-slate-500">
                Simpan riwayat skrining dan data kesehatan Anda
            </p>
        </div>
    </header>

    @if (session('status'))
        <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('status') }}
        </div>
    @endif

    <div class="rounded-2xl border border-brand-100 bg-white p-5 shadow-card">
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="text-xs font-medium text-slate-600">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="username"
                    class="mt-1 w-full rounded-xl border border-brand-200 bg-white px-3 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200"
                >
                @error('email')
                    <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="text-xs font-medium text-slate-600">Kata Sandi</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    class="mt-1 w-full rounded-xl border border-brand-200 bg-white px-3 py-2.5 text-sm text-slate-800 focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200"
                >
                @error('password')
                    <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <label for="remember_me" class="flex cursor-pointer items-center gap-2">
                <input
                    id="remember_me"
                    type="checkbox"
                    name="remember"
                    class="h-4 w-4 rounded border-brand-200 text-brand-600 focus:ring-brand-300"
                >
                <span class="text-sm text-slate-600">Ingat saya</span>
            </label>

            <button
                type="submit"
                class="w-full rounded-full bg-brand-600 py-3.5 text-sm font-semibold text-white shadow-soft transition hover:bg-brand-700 active:scale-[0.98]"
            >
                Masuk
            </button>
        </form>

        @if (Route::has('password.request'))
            <p class="mt-4 text-center">
                <a href="{{ route('password.request') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700">
                    Lupa kata sandi?
                </a>
            </p>
        @endif
    </div>

    @if (Route::has('register'))
        <p class="mt-6 text-center text-sm text-slate-500">
            Belum punya akun?
            <a href="{{ route('register') }}" class="font-semibold text-brand-600 hover:text-brand-700">Daftar</a>
        </p>
    @endif

    <a
        href="{{ route('home') }}"
        class="mt-4 flex w-full items-center justify-center gap-2 rounded-full border border-brand-200 bg-white py-3 text-sm font-semibold text-brand-600 transition hover:bg-brand-50 active:scale-[0.98]"
    >
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
        </svg>
        Kembali ke Beranda
    </a>
@endsection
