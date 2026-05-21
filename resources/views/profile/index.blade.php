@extends('layouts.mobile')

@section('content')
    <x-mobile.page-header title="Profil" />

    @auth
        <div class="mb-6 rounded-2xl bg-white p-5 shadow-card border border-brand-100 text-center">
            <div class="mx-auto mb-3 flex h-16 w-16 items-center justify-center rounded-full bg-brand-600 text-2xl font-bold text-white">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <p class="font-bold text-slate-900">{{ auth()->user()->name }}</p>
            <p class="text-sm text-slate-500">{{ auth()->user()->email }}</p>
        </div>

        <div class="space-y-2">
            <a href="{{ route('dashboard') }}" class="block rounded-2xl bg-white px-4 py-3 shadow-card border border-brand-100 font-semibold text-brand-600">Dashboard</a>
            <a href="{{ route('profile.edit') }}" class="block rounded-2xl bg-white px-4 py-3 shadow-card border border-brand-100 font-semibold text-slate-700">Edit Profil</a>
            @if (auth()->user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="block rounded-2xl bg-slate-800 px-4 py-3 font-semibold text-white">Admin Panel</a>
            @endif
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 font-semibold text-rose-600">Keluar</button>
            </form>
        </div>
    @else
        <div class="rounded-2xl bg-white p-6 text-center shadow-card">
            <p class="text-sm text-slate-500 mb-4">Masuk untuk menyimpan riwayat skrining dan data kesehatan Anda.</p>
            <a href="{{ route('login') }}" class="mb-2 block w-full rounded-full bg-brand-600 py-3 text-sm font-semibold text-white">Masuk</a>
            <a href="{{ route('register') }}" class="block w-full rounded-full border border-brand-200 py-3 text-sm font-semibold text-brand-600">Daftar Akun</a>
        </div>
    @endauth
@endsection
