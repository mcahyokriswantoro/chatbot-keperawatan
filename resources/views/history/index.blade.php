@extends('layouts.mobile')

@section('content')
    <x-mobile.page-header title="Riwayat Skrining" />

    <x-mobile.alert />

    <p class="mb-4 text-xs leading-relaxed text-slate-500">
        Riwayat skrining awal menampilkan penyakit yang perlu ditindaklanjuti. Setelah skrining lanjut selesai, hasil skor dan self management tersedia per penyakit.
    </p>

    @forelse ($sessions as $session)
        <div class="mb-4">
            @include('history.partials.session-card', ['session' => $session])
        </div>
    @empty
        <div class="rounded-2xl border border-dashed border-slate-200 bg-white p-8 text-center shadow-sm">
            <p class="text-sm font-medium text-slate-600">Belum ada riwayat skrining</p>
            <p class="mt-1 text-xs text-slate-400">Mulai percakapan dengan chatbot untuk mendapatkan skor dan rekomendasi</p>
            <a href="{{ route('detection.identity') }}" class="mt-4 inline-block rounded-full bg-brand-600 px-5 py-2.5 text-xs font-semibold text-white shadow-sm transition hover:bg-brand-700">
                Mulai Skrining
            </a>
        </div>
    @endforelse

    <div class="mt-2">{{ $sessions->links() }}</div>
@endsection
