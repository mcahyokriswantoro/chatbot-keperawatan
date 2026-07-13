@extends('layouts.admin')

@section('title', 'Video Edukasi')

@section('content')
    <x-admin.page-banner title="Video Edukasi" subtitle="Kelola video edukasi kesehatan" tone="violet" />

    <div class="mb-4 flex flex-wrap justify-end gap-2">
        <a href="{{ route('admin.articles.create') }}" class="inline-flex items-center gap-1.5 rounded-full bg-violet-600 px-4 py-2 text-xs font-semibold text-white shadow-soft">
            <span class="text-base leading-none">▶</span> Tambah video
        </a>
    </div>

    <div class="space-y-3">
        @forelse ($articles as $article)
            <article class="overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-sm">
                @if ($article->coverImageUrl())
                    <img src="{{ $article->coverImageUrl() }}" alt="" class="aspect-[16/9] w-full object-cover">
                @endif
                <div class="p-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="font-bold text-slate-900">{{ $article->title }}</p>
                        <p class="mt-0.5 text-xs text-slate-500">{{ $article->category }}</p>
                        <div class="mt-2 flex flex-wrap gap-1.5">
                            <span @class([
                                'inline-flex rounded-full px-2.5 py-0.5 text-[10px] font-semibold',
                                'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100' => $article->is_published,
                                'bg-slate-100 text-slate-600' => ! $article->is_published,
                            ])>
                                {{ $article->is_published ? 'Publik' : 'Draft' }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="mt-3 flex gap-3 text-xs font-semibold">
                    <a href="{{ route('admin.articles.edit', $article) }}" class="text-brand-600">Edit</a>
                    <form method="POST" action="{{ route('admin.articles.destroy', $article) }}" onsubmit="return confirm('Hapus video ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-rose-600">Hapus</button>
                    </form>
                </div>
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-200 bg-white p-8 text-center">
                <p class="text-sm text-slate-500">Belum ada video edukasi.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $articles->links() }}</div>
@endsection
