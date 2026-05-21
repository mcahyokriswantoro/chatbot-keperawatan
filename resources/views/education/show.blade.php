@extends('layouts.mobile')

@section('content')
    <x-mobile.page-header :title="$article->title" :back="route('education.index')" />

    <span class="rounded-full bg-brand-100 px-3 py-1 text-xs font-semibold text-brand-700">{{ $article->category }}</span>

    <div class="mt-4 rounded-2xl bg-white p-4 shadow-card border border-brand-100">
        <p class="mb-4 text-sm text-slate-500">{{ $article->excerpt }}</p>
        <div class="prose prose-sm max-w-none text-slate-700 whitespace-pre-wrap">{{ $article->content }}</div>
    </div>
@endsection
