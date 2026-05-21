@extends('layouts.admin')

@section('content')
    <h1 class="text-2xl font-bold mb-6">{{ $article->exists ? 'Edit' : 'Tambah' }} Artikel</h1>

    <form method="POST" action="{{ $article->exists ? route('admin.articles.update', $article) : route('admin.articles.store') }}" class="max-w-2xl space-y-4 bg-white rounded-xl p-6 shadow">
        @csrf
        @if ($article->exists) @method('PUT') @endif

        <div>
            <label class="block text-sm font-medium mb-1">Judul</label>
            <input type="text" name="title" value="{{ old('title', $article->title) }}" required class="w-full rounded-lg border px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Kategori</label>
            <input type="text" name="category" value="{{ old('category', $article->category) }}" required class="w-full rounded-lg border px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Ringkasan</label>
            <textarea name="excerpt" rows="2" required class="w-full rounded-lg border px-3 py-2">{{ old('excerpt', $article->excerpt) }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Konten</label>
            <textarea name="content" rows="8" required class="w-full rounded-lg border px-3 py-2">{{ old('content', $article->content) }}</textarea>
        </div>
        <label class="flex items-center gap-2">
            <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $article->is_published ?? true))>
            <span class="text-sm">Publikasikan</span>
        </label>
        <button type="submit" class="rounded-lg bg-brand-600 px-6 py-2 text-white font-semibold">Simpan</button>
    </form>
@endsection
