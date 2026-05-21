@extends('layouts.mobile')

@section('content')
    <x-mobile.page-header title="Riwayat Skrining" />

    <x-mobile.alert />

    @forelse ($sessions as $session)
        <a href="{{ route('history.show', $session->id) }}" class="mb-3 block rounded-2xl bg-white p-4 shadow-card border border-brand-100">
            <div class="flex items-start justify-between gap-2">
                <div>
                    <p class="font-semibold text-slate-900">
                        {{ $session->diseaseLabel() ?? 'Skrining Kesehatan' }}
                    </p>
                    <p class="text-xs text-slate-400">{{ $session->created_at->format('d M Y, H:i') }}</p>
                    <p class="mt-1 text-xs text-slate-500 line-clamp-2">{{ Str::limit($session->summary, 80) }}</p>
                </div>
                <span @class([
                    'shrink-0 rounded-full px-2.5 py-1 text-[10px] font-bold uppercase',
                    'bg-rose-100 text-rose-700' => $session->is_emergency,
                    'bg-amber-100 text-amber-700' => $session->risk_level === 'high' || $session->risk_level === 'medium',
                    'bg-emerald-100 text-emerald-700' => $session->risk_level === 'low',
                    'bg-red-600 text-white' => $session->risk_level === 'emergency',
                ])>
                    {{ $session->risk_level }}
                </span>
            </div>
        </a>
    @empty
        <div class="rounded-2xl bg-white p-6 text-center shadow-card">
            <p class="text-sm text-slate-500">Belum ada riwayat skrining.</p>
            <a href="{{ route('detection.identity') }}" class="mt-4 inline-block text-sm font-semibold text-brand-600">Mulai skrining →</a>
        </div>
    @endforelse

    <div class="mt-4">{{ $sessions->links() }}</div>
@endsection
