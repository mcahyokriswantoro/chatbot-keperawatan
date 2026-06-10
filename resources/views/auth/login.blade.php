@extends('layouts.auth')

@section('content')
    <x-auth.health-branding
        title="Masuk ke Akun"
        subtitle="Pilih masuk dengan email atau nomor HP yang sudah Anda daftarkan."
    />

    @if (session('status'))
        <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('status') }}
        </div>
    @endif

    <div class="rounded-2xl border border-brand-100 bg-white p-5 shadow-card">
        <form
            method="POST"
            action="{{ route('login') }}"
            class="space-y-4"
            x-data="{ method: @js(old('login_method', 'email')), showPassword: false }"
        >
            @csrf

            <div>
                <p class="mb-2 text-xs font-medium text-slate-600">Masuk dengan</p>
                <div class="grid grid-cols-2 gap-2 rounded-xl bg-slate-100 p-1">
                    <button
                        type="button"
                        @click="method = 'email'"
                        :class="method === 'email' ? 'bg-white text-brand-700 shadow-sm' : 'text-slate-500'"
                        class="rounded-lg py-2.5 text-xs font-semibold transition"
                    >
                        Email
                    </button>
                    <button
                        type="button"
                        @click="method = 'phone'"
                        :class="method === 'phone' ? 'bg-white text-brand-700 shadow-sm' : 'text-slate-500'"
                        class="rounded-lg py-2.5 text-xs font-semibold transition"
                    >
                        Nomor HP
                    </button>
                </div>
                <input type="hidden" name="login_method" :value="method">
                @error('login_method')<p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="login" class="text-xs font-medium text-slate-600" x-text="method === 'email' ? 'Email' : 'Nomor HP'"></label>
                <input
                    type="text"
                    id="login"
                    name="login"
                    value="{{ old('login') }}"
                    required
                    autofocus
                    :placeholder="method === 'email' ? 'nama@email.com' : '08xxxxxxxxxx'"
                    :inputmode="method === 'email' ? 'email' : 'tel'"
                    autocomplete="username"
                    class="mt-1 w-full rounded-xl border border-brand-200 bg-white px-3 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200"
                >
                @error('login')<p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="password" class="text-xs font-medium text-slate-600">Kata Sandi <span class="text-rose-500">*</span></label>
                <div class="relative mt-1">
                    <input
                        :type="showPassword ? 'text' : 'password'"
                        id="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        class="w-full rounded-xl border border-brand-200 bg-white py-2.5 pl-3 pr-11 text-sm text-slate-800 focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200"
                    >
                    <button
                        type="button"
                        @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-0 flex w-11 items-center justify-center text-slate-400 transition hover:text-brand-600"
                        :aria-label="showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
                    >
                        <svg x-show="!showPassword" x-cloak class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <svg x-show="showPassword" x-cloak class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>
                        </svg>
                    </button>
                </div>
                @error('password')<p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>@enderror
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
        Kembali ke Beranda
    </a>
@endsection
