@extends('layouts.mobile')

@section('content')
    <x-mobile.page-header title="Edukasi Kesehatan" />

    @forelse ($articles as $article)
        <a href="{{ route('education.show', $article->slug) }}" class="mb-3 block rounded-2xl bg-white p-4 shadow-card border border-brand-100">
            <span class="rounded-full bg-brand-100 px-2 py-0.5 text-[10px] font-semibold text-brand-700">{{ $article->category }}</span>
            <h2 class="mt-2 font-bold text-slate-900">{{ $article->title }}</h2>
            <p class="mt-1 text-sm text-slate-500">{{ $article->excerpt }}</p>
        </a>
    @empty
        <p class="text-sm text-slate-500 text-center py-8">Belum ada artikel edukasi.</p>
    @endforelse

    <div class="mt-4">{{ $articles->links() }}</div>
@endsection
