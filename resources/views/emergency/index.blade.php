@extends('layouts.mobile')

@section('content')
    <x-mobile.page-header title="Peringatan Darurat" />

    <div class="mb-6 rounded-2xl bg-rose-600 p-5 text-white shadow-card">
        <h2 class="text-lg font-bold">⚠️ Situasi Darurat?</h2>
        <p class="mt-2 text-sm text-rose-100">Segera hubungi layanan darurat atau kunjungi IGD terdekat.</p>
    </div>

    <section class="mb-6">
        <h2 class="mb-3 text-sm font-bold text-slate-900">Hotline Darurat</h2>
        @foreach ($hotlines as $hotline)
            <a href="tel:{{ $hotline['number'] }}" class="mb-3 flex items-center justify-between rounded-2xl bg-white p-4 shadow-card border border-rose-100">
                <div>
                    <p class="font-bold text-slate-900">{{ $hotline['name'] }}</p>
                    <p class="text-xs text-slate-500">{{ $hotline['description'] }}</p>
                </div>
                <span class="rounded-full bg-rose-600 px-4 py-2 text-sm font-bold text-white">{{ $hotline['number'] }}</span>
            </a>
        @endforeach
    </section>

    <section>
        <h2 class="mb-3 text-sm font-bold text-slate-900">Peringatan Penting</h2>
        <ul class="space-y-2">
            @foreach ($warnings as $warning)
                <li class="rounded-2xl bg-amber-50 border border-amber-200 px-4 py-3 text-sm text-amber-900">{{ $warning }}</li>
            @endforeach
        </ul>
    </section>
@endsection
