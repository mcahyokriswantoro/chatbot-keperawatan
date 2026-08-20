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
            <div class="flex justify-between gap-4 border-b border-slate-50 pb-2"><dt class="text-slate-500">Pendidikan</dt><dd class="font-medium text-right">{{ $user->education ?? '—' }}</dd></div>
            <div class="flex justify-between gap-4 border-b border-slate-50 pb-2"><dt class="text-slate-500">Pekerjaan</dt><dd class="font-medium text-right">{{ $user->occupation ?? '—' }}</dd></div>
            <div><dt class="text-slate-500 text-xs mb-1">Alamat</dt><dd class="text-sm leading-relaxed">{{ $user->address ?? '—' }}</dd></div>
        </dl>
    </section>

    <section class="mb-5">
        <h2 class="mb-3 text-sm font-bold text-slate-900">Riwayat skrining</h2>

        @forelse ($screeningIdentities as $identityIndex => $identity)
            @php
                $label = $user->name[0] . '.' . ($identityIndex + 1);
                $isSelf = $identity->screening_target === 'self';
            @endphp

            {{-- Identity header --}}
            <div class="mb-2 mt-4 first:mt-0">
                <div class="flex items-center gap-2">
                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-brand-600 text-[11px] font-bold text-white">
                        {{ $label }}
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold text-slate-900">{{ $identity->name }}</p>
                        <p class="text-[10px] text-slate-400">
                            {{ $identity->gender ? ucfirst($identity->gender) : '—' }}
                            @if($identity->date_of_birth)· {{ $identity->date_of_birth->format('d/m/Y') }}@endif
                            @if($identity->domicile_address)· {{ Str::limit($identity->domicile_address, 30) }}@endif
                        </p>
                    </div>
                    @if ($isSelf)
                        <span class="shrink-0 rounded-full bg-brand-50 px-2 py-0.5 text-[10px] font-semibold text-brand-700 ring-1 ring-brand-100">
                            Diri sendiri
                        </span>
                    @else
                        <span class="shrink-0 rounded-full bg-amber-50 px-2 py-0.5 text-[10px] font-semibold text-amber-700 ring-1 ring-amber-100">
                            Orang lain
                        </span>
                    @endif
                </div>
            </div>

            {{-- Sessions under this identity --}}
            @if ($identity->screeningSessions->isEmpty())
                <div class="ml-9 rounded-xl border border-dashed border-slate-200 bg-white px-4 py-3 text-center text-xs text-slate-400">
                    Belum ada skrining untuk identitas ini.
                </div>
            @else
                <div class="ml-9 space-y-2">
                    @foreach ($identity->screeningSessions as $s)
                        @php($theme = $s->riskTheme())
                        <a href="{{ route('admin.screenings.show', $s) }}"
                           class="block overflow-hidden rounded-xl border bg-white shadow-sm transition active:scale-[0.99] {{ $theme['border'] }}">
                            <div class="flex">
                                <div @class(['w-1 shrink-0', $theme['accent']])></div>
                                <div class="min-w-0 flex-1 px-3 py-2.5">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-slate-900">{{ $s->diseaseLabel() ?? 'Skrining' }}</p>
                                            <p class="mt-0.5 text-[10px] text-slate-400">{{ $s->formattedDateTime() }}</p>
                                            @if ($s->scoreSummary())
                                                <p class="mt-1 text-[11px] font-medium text-slate-600">{{ $s->scoreSummary() }}</p>
                                            @endif
                                        </div>
                                        @include('admin.partials.risk-badge', ['session' => $s])
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        @empty
            <p class="rounded-2xl border border-dashed border-slate-200 bg-white p-6 text-center text-sm text-slate-500">Belum ada skrining.</p>
        @endforelse
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
