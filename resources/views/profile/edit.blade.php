@extends('layouts.mobile')

@section('content')
    <x-mobile.page-header title="Edit Profil" :back="route('profile.page')" />

    <div class="mb-4 flex items-center gap-4 rounded-2xl border border-brand-100 bg-white p-4 shadow-card">
        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-brand-600 text-xl font-bold text-white">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <div class="min-w-0 flex-1">
            <p class="truncate font-bold text-slate-900">{{ $user->name }}</p>
            <p class="truncate text-sm text-slate-500">{{ $user->email }}</p>
        </div>
    </div>

    <div class="space-y-4">
        <div class="rounded-2xl border border-brand-100 bg-white p-5 shadow-card">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="rounded-2xl border border-brand-100 bg-white p-5 shadow-card">
            @include('profile.partials.update-password-form')
        </div>

        <div class="rounded-2xl border border-rose-100 bg-white p-5 shadow-card">
            @include('profile.partials.delete-user-form')
        </div>
    </div>

    <a
        href="{{ route('profile.page') }}"
        class="mt-6 flex w-full items-center justify-center gap-2 rounded-full border border-brand-200 bg-white py-3 text-sm font-semibold text-brand-600 transition hover:bg-brand-50 active:scale-[0.98]"
    >
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
        </svg>
        Kembali ke Profil
    </a>
@endsection
