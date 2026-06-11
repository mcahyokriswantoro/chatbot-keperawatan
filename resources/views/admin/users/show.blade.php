@extends('layouts.admin')

@section('title', $user->name)

@section('content')
    <x-admin.page-banner
        :title="$user->name"
        :subtitle="$user->email"
        :back="route('admin.users.index')"
        tone="brand"
        :show-actions="false"
    />

    <div class="mb-5 grid grid-cols-2 gap-3">
        <div class="rounded-2xl border border-brand-100 bg-white p-4 text-center shadow-sm">
            <p class="text-2xl font-bold text-brand-600">{{ $user->screening_sessions_count }}</p>
            <p class="mt-1 text-[11px] text-slate-500">Skrining</p>
        </div>
        <div class="rounded-2xl border border-brand-100 bg-white p-4 text-center shadow-sm">
            <p class="text-2xl font-bold text-emerald-600">{{ $user->health_monitorings_count }}</p>
            <p class="mt-1 text-[11px] text-slate-500">Monitoring</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.access.store') }}" class="mb-5">
        @csrf
        <input type="hidden" name="email" value="{{ $user->email }}">
        <button type="submit" class="w-full rounded-full border border-amber-200 bg-amber-50 py-2.5 text-xs font-semibold text-amber-900 transition hover:bg-amber-100">
            🔐 Jadikan admin
        </button>
    </form>

    <section class="mb-5 rounded-2xl border border-brand-100 bg-white p-4 shadow-sm">
        <h2 class="mb-3 text-sm font-bold text-slate-900">Profil</h2>
        <dl class="space-y-3 text-sm">
            <div class="flex justify-between gap-4 border-b border-slate-50 pb-2"><dt class="text-slate-500">Jenis kelamin</dt><dd class="font-medium text-right">{{ $user->genderLabel() ?? '—' }}</dd></div>
            <div class="flex justify-between gap-4 border-b border-slate-50 pb-2"><dt class="text-slate-500">Telepon</dt><dd class="font-medium text-right">{{ $user->phone ?? '—' }}</dd></div>
            <div class="flex justify-between gap-4 border-b border-slate-50 pb-2"><dt class="text-slate-500">Tanggal lahir</dt><dd class="font-medium text-right">{{ $user->date_of_birth?->format('d/m/Y') ?? '—' }}</dd></div>
            <div class="flex justify-between gap-4 border-b border-slate-50 pb-2"><dt class="text-slate-500">Berat / tinggi</dt><dd class="font-medium text-right">{{ $user->weight ? $user->weight.' kg' : '—' }} / {{ $user->height ? $user->height.' cm' : '—' }}</dd></div>
            <div class="flex justify-between gap-4 border-b border-slate-50 pb-2"><dt class="text-slate-500">Pekerjaan</dt><dd class="font-medium text-right">{{ $user->occupation ?? '—' }}</dd></div>
            <div><dt class="text-slate-500 text-xs mb-1">Alamat</dt><dd class="text-sm leading-relaxed">{{ $user->address ?? '—' }}</dd></div>
        </dl>
    </section>

    <section class="mb-5">
        <h2 class="mb-3 text-sm font-bold text-slate-900">Riwayat skrining</h2>
        <div class="space-y-3">
            @forelse ($screenings as $s)
                @include('admin.partials.screening-list-card', [
                    'session' => $s,
                    'detailUrl' => route('admin.screenings.show', $s),
                ])
            @empty
                <p class="rounded-2xl border border-dashed border-slate-200 bg-white p-6 text-center text-sm text-slate-500">Belum ada skrining.</p>
            @endforelse
        </div>
    </section>

    <section>
        <h2 class="mb-3 text-sm font-bold text-slate-900">Monitoring</h2>
        <div class="space-y-3">
            @forelse ($monitoring as $m)
                @include('admin.partials.monitoring-list-card', ['entry' => $m])
            @empty
                <p class="rounded-2xl border border-dashed border-slate-200 bg-white p-6 text-center text-sm text-slate-500">Belum ada monitoring.</p>
            @endforelse
        </div>
    </section>
@endsection
