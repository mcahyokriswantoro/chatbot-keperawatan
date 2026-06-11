@extends('layouts.admin')

@section('title', 'Pengguna')

@section('content')
    <x-admin.page-banner title="Pengguna Terdaftar" :subtitle="$totalUsers.' akun terdaftar'" tone="brand" />

    <a href="{{ route('admin.access.index') }}" class="mb-4 flex items-center justify-center gap-1.5 rounded-full border border-amber-200 bg-amber-50 px-4 py-2 text-xs font-semibold text-amber-800">
        🔐 Kelola akses admin
    </a>

    <form method="GET" class="mb-4 flex gap-2">
        <input
            type="search"
            name="q"
            value="{{ $search }}"
            placeholder="Cari nama, email, telepon..."
            class="min-w-0 flex-1 rounded-xl border border-brand-200 bg-white px-3 py-2.5 text-sm focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200"
        >
        <button type="submit" class="shrink-0 rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white">Cari</button>
    </form>

    <div class="space-y-3">
        @forelse ($users as $user)
            @include('admin.partials.user-list-card', ['user' => $user])
        @empty
            <div class="rounded-2xl border border-dashed border-brand-200 bg-brand-50/30 p-8 text-center">
                <img src="{{ asset('images/robot.png') }}" alt="" class="mx-auto h-14 w-14 object-contain opacity-75">
                <p class="mt-3 text-sm text-slate-500">Tidak ada pengguna ditemukan.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $users->links() }}</div>
@endsection
