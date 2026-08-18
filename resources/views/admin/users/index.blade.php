@extends('layouts.admin')

@section('title', 'Pengguna')

@section('content')
    <x-admin.page-banner title="Pengguna Terdaftar" :subtitle="$totalUsers.' akun terdaftar'" tone="brand" />

    {{-- Action Bar --}}
    <div class="mb-4 flex flex-wrap items-center gap-2">
        <a href="{{ route('admin.access.index') }}" class="flex items-center gap-1.5 rounded-full border border-amber-200 bg-amber-50 px-3.5 py-2 text-xs font-semibold text-amber-800 transition hover:bg-amber-100">
            🔐 Kelola akses admin
        </a>
        <div class="ml-auto flex items-center gap-2">
            <a href="{{ route('admin.users.rekap') }}"
               class="flex items-center gap-1.5 rounded-full border border-brand-200 bg-brand-50 px-3.5 py-2 text-xs font-semibold text-brand-800 shadow-sm transition hover:bg-brand-100 active:scale-95">
                <svg class="h-4 w-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Lihat Rekap Pasien
            </a>
            <a href="{{ route('admin.users.export') }}"
               class="flex items-center gap-1.5 rounded-full border border-emerald-300 bg-emerald-50 px-3.5 py-2 text-xs font-semibold text-emerald-800 shadow-sm transition hover:bg-emerald-100 hover:border-emerald-400 active:scale-95">
                <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                </svg>
                Export .xls
            </a>
        </div>
    </div>

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
