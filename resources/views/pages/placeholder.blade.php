@extends('layouts.mobile')

@section('content')
    <div class="flex flex-col items-center justify-center min-h-[60vh] text-center px-4">
        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-brand-100 text-brand-600 mb-4">
            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.94m-1.06 1.06l5.94-6.837a2.548 2.548 0 113.586 3.586L9.83 11.42"/>
            </svg>
        </div>
        <h1 class="text-xl font-bold text-slate-900">{{ $title }}</h1>
        <p class="mt-2 text-sm text-slate-500">Halaman ini sedang dalam pengembangan.</p>
        <a href="{{ route('home') }}" class="mt-6 text-sm font-semibold text-brand-600 hover:text-brand-700">
            ← Kembali ke Beranda
        </a>
    </div>
@endsection
