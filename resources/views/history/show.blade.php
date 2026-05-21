@extends('layouts.mobile')

@section('content')
    <x-mobile.page-header title="Detail Skrining" :back="route('history')" />

    <div class="mb-4 flex items-center justify-between">
        <p class="text-sm text-slate-500">{{ $session->created_at->format('d M Y, H:i') }}</p>
        @if ($session->is_emergency)
            <span class="rounded-full bg-rose-600 px-3 py-1 text-xs font-bold text-white">DARURAT</span>
        @endif
    </div>

    <div class="rounded-2xl bg-white p-4 shadow-card border border-brand-100">
        <pre class="whitespace-pre-wrap font-sans text-sm leading-relaxed text-slate-700">{{ $session->summary }}</pre>
    </div>

    @if ($session->is_emergency)
        <a href="{{ route('emergency') }}" class="mt-4 flex w-full items-center justify-center rounded-full bg-rose-600 py-3 text-sm font-bold text-white">
            Lihat Peringatan Darurat
        </a>
    @endif
@endsection
