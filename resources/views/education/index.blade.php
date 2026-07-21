@extends('layouts.mobile')

@section('content')
<div
    x-data="{
        search: '',
        category: 'all',
        preview: null,
        saved: JSON.parse(localStorage.getItem('edu_saved') ?? '[]'),
        articles: @js($articles),
        categories: @js($categories),
        filtered() {
            return this.articles.filter(a => {
                const matchCat = this.category === 'all' || a.category_id === this.category;

                if (!matchCat) return false;
                if (!this.search.trim()) return true;

                const q = this.search.toLowerCase();
                return a.title.toLowerCase().includes(q)
                    || a.tag.toLowerCase().includes(q)
                    || a.category_name.toLowerCase().includes(q);
            });
        },
        isSaved(slug) {
            return this.saved.includes(slug);
        },
        toggleSave(slug) {
            if (this.isSaved(slug)) {
                this.saved = this.saved.filter(s => s !== slug);
            } else {
                this.saved = [...this.saved, slug];
            }
            localStorage.setItem('edu_saved', JSON.stringify(this.saved));
        },
        openPreview(article) {
            this.preview = article;
            document.body.style.overflow = 'hidden';
        },
        closePreview() {
            this.preview = null;
            document.body.style.overflow = '';
        },
    }"
    @keydown.escape.window="closePreview()"
    class="space-y-5 pb-2"
>
    <header class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-violet-600 via-brand-600 to-teal-500 px-4 pb-5 pt-4 text-white shadow-lg">
        <div class="pointer-events-none absolute -right-6 -top-6 h-28 w-28 rounded-full bg-white/10 blur-xl"></div>
        <div class="relative flex items-start gap-3">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-white/20 text-2xl ring-2 ring-white/30 backdrop-blur-sm" aria-hidden="true">▶️</div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-violet-100">Nersia Health</p>
                <h1 class="text-xl font-bold leading-tight">Video Edukasi</h1>
                <p class="mt-1 text-xs text-white/80">Video edukasi kesehatan dari tim kesehatan</p>
            </div>
        </div>
    </header>

    <div class="relative">
        <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
        <input
            type="search"
            x-model="search"
            placeholder="Cari video edukasi..."
            class="w-full rounded-2xl border border-brand-100 bg-white py-3 pl-10 pr-4 text-sm shadow-sm placeholder:text-slate-400 focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-100"
        />
    </div>

    <div class="flex gap-2 overflow-x-auto pb-1 scrollbar-none">
        <button
            type="button"
            @click="category = 'all'"
            :class="category === 'all' ? 'bg-brand-600 text-white shadow-sm' : 'bg-white text-slate-600 ring-1 ring-slate-200'"
            class="shrink-0 rounded-full px-3.5 py-2 text-[11px] font-semibold transition active:scale-95"
        >
            Semua
        </button>
        <template x-for="cat in categories" :key="cat.id">
            <button
                type="button"
                @click="category = cat.id"
                :class="category === cat.id ? 'bg-brand-600 text-white shadow-sm' : 'bg-white text-slate-600 ring-1 ring-slate-200'"
                class="shrink-0 rounded-full px-3.5 py-2 text-[11px] font-semibold transition active:scale-95"
            >
                <span x-text="cat.icon + ' ' + cat.name"></span>
            </button>
        </template>
    </div>

    <div x-show="saved.length > 0" x-cloak class="rounded-2xl border border-amber-100 bg-amber-50/80 px-4 py-3">
        <p class="text-[11px] font-bold text-amber-900">⭐ Tersimpan (<span x-text="saved.length"></span>)</p>
        <p class="mt-0.5 text-[10px] text-amber-700">Tap ikon bookmark pada kartu untuk simpan tonton nanti</p>
    </div>

    @if ($articles->isEmpty())
        <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-8 text-center">
            <p class="text-3xl" aria-hidden="true">▶️</p>
            <p class="mt-2 text-sm font-semibold text-slate-800">Belum ada video edukasi</p>
            <p class="mt-1 text-xs text-slate-500">Admin dapat menambahkan video di panel Edukasi.</p>
        </div>
    @endif

    <section x-show="filtered().length > 0">
        <div class="mb-3 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900">Daftar Video</h2>
            <span class="text-[11px] font-medium text-slate-500" x-text="filtered().length + ' video'"></span>
        </div>

        <div x-show="filtered().length === 0" x-cloak class="rounded-2xl border border-dashed border-slate-200 bg-white px-4 py-10 text-center">
            <p class="text-sm font-medium text-slate-600">Video tidak ditemukan</p>
            <p class="mt-1 text-xs text-slate-400">Coba kata kunci atau kategori lain</p>
            <button type="button" @click="search = ''; category = 'all'" class="mt-3 text-xs font-semibold text-brand-600">Reset filter</button>
        </div>

        <div class="grid gap-4">
            <template x-for="article in filtered()" :key="article.slug">
                <article class="overflow-hidden rounded-2xl border border-brand-50 bg-white shadow-sm transition hover:shadow-md">
                    <div class="relative aspect-[2/1] overflow-hidden bg-slate-100">
                        <img
                            :src="article.image"
                            :alt="article.title"
                            class="h-full w-full object-cover"
                            loading="lazy"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                        <span
                            class="absolute left-3 top-3 rounded-full px-2 py-0.5 text-[10px] font-bold ring-1 backdrop-blur-sm bg-violet-50 text-violet-700 ring-violet-200"
                            x-text="'▶️ ' + article.category_name"
                        ></span>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="flex h-14 w-14 items-center justify-center rounded-full bg-white/90 text-2xl shadow-lg">▶</span>
                        </div>
                        <button
                            type="button"
                            @click.stop="toggleSave(article.slug)"
                            class="absolute right-3 top-3 flex h-8 w-8 items-center justify-center rounded-full bg-white/90 shadow-sm backdrop-blur-sm transition active:scale-90"
                            :aria-label="isSaved(article.slug) ? 'Hapus dari tersimpan' : 'Simpan'"
                        >
                            <svg
                                class="h-4 w-4 transition"
                                :class="isSaved(article.slug) ? 'fill-amber-400 text-amber-500' : 'text-slate-400'"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            ><path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z"/></svg>
                        </button>
                    </div>

                    <div class="p-4">
                        <div class="mb-2 flex items-center gap-2 text-[10px] font-medium text-slate-400">
                            <span x-text="article.read_label"></span>
                            <span>·</span>
                            <span class="rounded-full bg-slate-100 px-2 py-0.5 text-slate-600" x-text="article.tag"></span>
                            <template x-if="article.published_at">
                                <span>· <span x-text="article.published_at"></span></span>
                            </template>
                        </div>
                        <h3 class="font-bold leading-snug text-slate-900" x-text="article.title"></h3>

                        <div class="mt-3 flex gap-2">
                            <button
                                type="button"
                                @click="openPreview(article)"
                                class="flex-1 rounded-xl bg-brand-50 py-2.5 text-center text-xs font-semibold text-brand-700 transition hover:bg-brand-100 active:scale-[0.98]"
                            >
                                Preview
                            </button>
                            <a
                                :href="article.url"
                                class="flex flex-1 items-center justify-center gap-1 rounded-xl bg-brand-600 py-2.5 text-xs font-semibold text-white transition hover:bg-brand-700 active:scale-[0.98]"
                            >
                                Tonton
                            </a>
                        </div>
                    </div>
                </article>
            </template>
        </div>
    </section>

    <div
        x-show="preview"
        x-cloak
        class="fixed inset-0 z-[60] flex items-end justify-center bg-black/50 p-0 backdrop-blur-sm sm:items-center sm:p-4"
        @click.self="closePreview()"
    >
        <div
            x-show="preview"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-8"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-8"
            class="max-h-[90vh] w-full max-w-md overflow-hidden rounded-t-3xl bg-white shadow-2xl sm:rounded-3xl"
            @click.stop
        >
            <template x-if="preview">
                <div>
                    <div class="relative aspect-video overflow-hidden bg-slate-200">
                        <img :src="preview.image" :alt="preview.title" class="h-full w-full object-cover" />
                        <div class="absolute inset-0 flex items-center justify-center bg-black/20">
                            <span class="flex h-14 w-14 items-center justify-center rounded-full bg-white/90 text-2xl shadow-lg">▶</span>
                        </div>
                        <button
                            type="button"
                            @click="closePreview()"
                            class="absolute right-3 top-3 flex h-9 w-9 items-center justify-center rounded-full bg-black/40 text-white backdrop-blur-sm"
                            aria-label="Tutup"
                        >
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div class="max-h-[45vh] overflow-y-auto p-5">
                        <span class="inline-flex rounded-full bg-violet-50 px-2.5 py-0.5 text-[10px] font-bold text-violet-700 ring-1 ring-violet-200">
                            ▶️ Video
                        </span>
                        <h2 class="mt-3 text-lg font-bold leading-snug text-slate-900" x-text="preview.title"></h2>
                        <p class="mt-1 text-[11px] text-slate-400" x-text="preview.read_label"></p>
                    </div>
                    <div class="flex gap-2 border-t border-slate-100 p-4">
                        <button
                            type="button"
                            @click="toggleSave(preview.slug)"
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white transition active:scale-95"
                        >
                            <svg
                                class="h-5 w-5"
                                :class="isSaved(preview.slug) ? 'fill-amber-400 text-amber-500' : 'text-slate-400'"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            ><path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z"/></svg>
                        </button>
                        <a
                            :href="preview.url"
                            class="flex flex-1 items-center justify-center gap-2 rounded-xl bg-brand-600 py-3 text-sm font-semibold text-white transition hover:bg-brand-700 active:scale-[0.98]"
                        >
                            Tonton video
                        </a>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
    .scrollbar-none::-webkit-scrollbar { display: none; }
    .scrollbar-none { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endpush
