@extends('layouts.admin')

@section('title', $article->exists ? 'Edit Artikel' : 'Tambah Artikel')

@section('content')
    <x-admin.page-banner
        :title="$article->exists ? 'Edit Artikel' : 'Tambah Artikel'"
        :back="route('admin.articles.index')"
        tone="violet"
        :show-actions="false"
    />

    <form
        method="POST"
        action="{{ $article->exists ? route('admin.articles.update', $article) : route('admin.articles.store') }}"
        enctype="multipart/form-data"
        class="space-y-4 rounded-2xl border border-brand-100 bg-white p-4 shadow-sm"
        x-data="{
            preview: @js($article->coverImageUrl()),
            onFileChange(event) {
                const file = event.target.files[0];
                if (! file) return;
                this.preview = URL.createObjectURL(file);
            },
        }"
    >
        @csrf
        @if ($article->exists) @method('PUT') @endif

        <div>
            <label class="mb-2 block text-xs font-medium text-slate-600">Foto artikel</label>
            <div class="overflow-hidden rounded-2xl border border-dashed border-brand-200 bg-brand-50/40">
                <div class="relative aspect-[16/9] bg-slate-100">
                    <template x-if="preview">
                        <img :src="preview" alt="" class="h-full w-full object-cover">
                    </template>
                    <template x-if="! preview">
                        <div class="flex h-full flex-col items-center justify-center gap-2 text-slate-400">
                            <svg class="h-10 w-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0 0 22.5 18.75V5.25A2.25 2.25 0 0 0 20.25 3H3.75A2.25 2.25 0 0 0 1.5 5.25v13.5A2.25 2.25 0 0 0 3.75 21Z"/></svg>
                            <p class="text-[11px] font-medium">Belum ada foto</p>
                        </div>
                    </template>
                </div>
                <div class="border-t border-brand-100 p-3">
                    <input
                        type="file"
                        name="cover_image"
                        accept="image/jpeg,image/png,image/webp"
                        @change="onFileChange($event)"
                        class="block w-full text-xs text-slate-600 file:mr-3 file:rounded-full file:border-0 file:bg-brand-600 file:px-4 file:py-2 file:text-xs file:font-semibold file:text-white hover:file:bg-brand-700"
                    >
                    <p class="mt-2 text-[10px] text-slate-500">JPG, PNG, atau WebP · maks. 2 MB · disarankan landscape 16:9</p>
                    @error('cover_image')
                        <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            @if ($article->coverImageUrl())
                <label class="mt-3 flex items-center gap-2 text-xs text-slate-600">
                    <input type="checkbox" name="remove_cover_image" value="1" class="rounded border-brand-200 text-brand-600">
                    Hapus foto saat ini
                </label>
            @endif
        </div>

        <div>
            <label class="mb-1 block text-xs font-medium text-slate-600">Judul</label>
            <input type="text" name="title" value="{{ old('title', $article->title) }}" required class="w-full rounded-xl border border-brand-200 px-3 py-2.5 text-sm focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200">
            @error('title')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-slate-600">Kategori</label>
            <input type="text" name="category" value="{{ old('category', $article->category) }}" required class="w-full rounded-xl border border-brand-200 px-3 py-2.5 text-sm">
            @error('category')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-slate-600">Ringkasan</label>
            <textarea name="excerpt" rows="2" required class="w-full rounded-xl border border-brand-200 px-3 py-2.5 text-sm">{{ old('excerpt', $article->excerpt) }}</textarea>
            @error('excerpt')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-slate-600">Konten</label>
            <textarea name="content" rows="8" required class="w-full rounded-xl border border-brand-200 px-3 py-2.5 text-sm">{{ old('content', $article->content) }}</textarea>
            @error('content')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $article->is_published ?? true)) class="rounded border-brand-200 text-brand-600">
            <span>Publikasikan</span>
        </label>
        <button type="submit" class="w-full rounded-full bg-brand-600 py-3.5 text-sm font-semibold text-white shadow-soft">Simpan</button>
    </form>
@endsection
