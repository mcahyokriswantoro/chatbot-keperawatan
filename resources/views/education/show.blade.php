@extends('layouts.mobile')

@section('content')
    <x-mobile.page-header :title="$article->title" :back="route('education.index')" />

    <div class="flex flex-wrap items-center gap-2">
        <span class="rounded-full bg-brand-100 px-3 py-1 text-xs font-semibold text-brand-700">{{ $article->category }}</span>
        <span class="rounded-full bg-violet-100 px-3 py-1 text-xs font-semibold text-violet-700">Video</span>
    </div>

    @if ($article->videoPlaybackUrl())
        <div class="mt-4 overflow-hidden rounded-2xl border border-brand-100 bg-black shadow-sm">
            @if ($article->isDirectVideoFile())
                <video controls playsinline preload="metadata" class="aspect-video w-full" @if ($article->coverImageUrl()) poster="{{ $article->coverImageUrl() }}" @endif>
                    <source src="{{ $article->videoPlaybackUrl() }}" @if ($article->videoMimeType()) type="{{ $article->videoMimeType() }}" @endif>
                    Peramban Anda tidak mendukung pemutaran video.
                </video>
            @else
                <iframe
                    src="{{ $article->videoEmbedUrl() }}"
                    title="{{ $article->title }}"
                    class="aspect-video w-full"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    allowfullscreen
                ></iframe>
            @endif
        </div>
    @elseif ($article->coverImageUrl())
        <div class="mt-4 overflow-hidden rounded-2xl border border-brand-100 shadow-sm">
            <img src="{{ $article->coverImageUrl() }}" alt="" class="aspect-[16/9] w-full object-cover">
        </div>
    @endif
@endsection
