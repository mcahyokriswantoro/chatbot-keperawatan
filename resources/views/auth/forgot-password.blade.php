@extends('layouts.auth')

@section('content')
    <x-auth.health-branding
        title="Lupa Kata Sandi?"
        subtitle="Masukkan alamat email Anda dan kami akan mengirimkan tautan untuk mengatur ulang kata sandi."
    />

    @if (session('status'))
        <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('status') }}
        </div>
    @endif

    <div class="rounded-2xl border border-brand-100 bg-white p-5 shadow-card">
        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf

            {{-- Ikon amplop --}}
            <div class="flex justify-center py-2">
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-brand-50 ring-4 ring-brand-100">
                    <svg class="h-8 w-8 text-brand-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                    </svg>
                </div>
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="text-xs font-medium text-slate-600">
                    Alamat Email <span class="text-rose-500">*</span>
                </label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="email"
                    placeholder="nama@email.com"
                    class="mt-1 w-full rounded-xl border border-brand-200 bg-white px-3 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200 @error('email') border-rose-400 focus:border-rose-400 focus:ring-rose-200 @enderror"
                >
                @error('email')
                    <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tombol kirim --}}
            <button
                type="submit"
                class="w-full rounded-full bg-brand-600 py-3.5 text-sm font-semibold text-white shadow-soft transition hover:bg-brand-700 active:scale-[0.98]"
            >
                Kirim Tautan Reset
            </button>
        </form>
    </div>

    {{-- Kembali ke login --}}
    <p class="mt-6 text-center text-sm text-slate-500">
        Sudah ingat kata sandi?
        <a href="{{ route('login') }}" class="font-semibold text-brand-600 hover:text-brand-700">Masuk</a>
    </p>

    <a
        href="{{ route('home') }}"
        class="mt-4 flex w-full items-center justify-center gap-2 rounded-full border border-brand-200 bg-white py-3 text-sm font-semibold text-brand-600 transition hover:bg-brand-50 active:scale-[0.98]"
    >
        Kembali ke Beranda
    </a>
@endsection
