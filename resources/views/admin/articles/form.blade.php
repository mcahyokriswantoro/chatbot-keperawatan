@extends('layouts.admin')

@php
    $isVideo = true;
    $defaultVideoSource = old('video_source', $article->hasStoredVideo() ? 'server' : (($article->video_url && ! $article->hasStoredVideo()) ? 'link' : 'file'));
    $videoLink = old('video_link', ($article->video_url && ! $article->hasStoredVideo()) ? $article->video_url : '');
    $defaultVideoPath = old('video_path', $article->hasStoredVideo() ? $article->video_url : '');
    $serverVideos = $serverVideos ?? [];
@endphp

@section('title', $article->exists ? 'Edit Video' : 'Tambah Video')

@section('content')
    <x-admin.page-banner
        :title="$article->exists ? 'Edit Video' : 'Tambah Video'"
        :back="route('admin.articles.index')"
        tone="violet"
        :show-actions="false"
    />

    <form
        method="POST"
        action="{{ $article->exists ? route('admin.articles.update', $article) : route('admin.articles.store') }}"
        enctype="multipart/form-data"
        class="space-y-4 rounded-2xl border border-brand-100 bg-white p-4 shadow-sm"
        x-data="adminArticleForm({
            isVideo: @js($isVideo),
            existingCover: @js($article->coverImageUrl()),
            existingVideoUrl: @js($isVideo ? $article->videoPlaybackUrl() : null),
            videoMode: @js($defaultVideoSource),
            defaultVideoPath: @js($defaultVideoPath),
            serverVideos: @js($serverVideos),
        })"
        @submit="onSubmit($event)"
    >
        @csrf
        @if ($article->exists) @method('PUT') @endif
        <input type="hidden" name="content_type" value="video">

        @if (session('upload_error'))
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                {{ session('upload_error') }}
            </div>
        @endif

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

        @if ($isVideo)
            <div>
                <label class="mb-2 block text-xs font-medium text-slate-600">Sumber video</label>
                <div class="mb-3 flex flex-wrap gap-2">
                    <label class="inline-flex cursor-pointer items-center gap-2 rounded-full border px-3 py-2 text-[11px] font-semibold transition" :class="videoMode === 'file' ? 'border-violet-600 bg-violet-50 text-violet-700' : 'border-slate-200 bg-white text-slate-600'">
                        <input type="radio" name="video_source" value="file" x-model="videoMode" class="sr-only">
                        Upload file
                    </label>
                    <label class="inline-flex cursor-pointer items-center gap-2 rounded-full border px-3 py-2 text-[11px] font-semibold transition" :class="videoMode === 'link' ? 'border-violet-600 bg-violet-50 text-violet-700' : 'border-slate-200 bg-white text-slate-600'">
                        <input type="radio" name="video_source" value="link" x-model="videoMode" class="sr-only">
                        Link video
                    </label>
                    @if (count($serverVideos) > 0)
                        <label class="inline-flex cursor-pointer items-center gap-2 rounded-full border px-3 py-2 text-[11px] font-semibold transition" :class="videoMode === 'server' ? 'border-violet-600 bg-violet-50 text-violet-700' : 'border-slate-200 bg-white text-slate-600'">
                            <input type="radio" name="video_source" value="server" x-model="videoMode" class="sr-only">
                            Sudah di server
                        </label>
                    @endif
                </div>
                <p class="mb-3 rounded-xl bg-amber-50 px-3 py-2 text-[10px] leading-relaxed text-amber-800">
                    Jika upload file error 500, gunakan <strong>Sudah di server</strong> (pilih file yang sudah diunggah lewat File Manager) atau <strong>Link video</strong> (YouTube/Vimeo). Upload file besar sering diblokir ModSecurity hosting.
                </p>

                <div x-show="videoMode === 'file'" x-cloak>
                    <label class="mb-2 block text-xs font-medium text-slate-600">File video</label>
                    <div class="overflow-hidden rounded-2xl border border-dashed border-violet-200 bg-violet-50/40">
                        @if ($article->videoPlaybackUrl() && $article->hasStoredVideo())
                            <div class="border-b border-violet-100 bg-black" x-show="! videoObjectUrl" x-cloak>
                                <video controls playsinline preload="metadata" class="aspect-video w-full">
                                    <source src="{{ $article->videoPlaybackUrl() }}" @if ($article->videoMimeType()) type="{{ $article->videoMimeType() }}" @endif>
                                </video>
                            </div>
                        @endif
                        <div class="p-3">
                            <input
                                type="file"
                                name="video_file"
                                accept="video/mp4,video/webm,video/ogg,video/quicktime,.mov"
                                @change="onVideoFileChange($event)"
                                class="block w-full text-xs text-slate-600 file:mr-3 file:rounded-full file:border-0 file:bg-violet-600 file:px-4 file:py-2 file:text-xs file:font-semibold file:text-white hover:file:bg-violet-700"
                            >
                            <p class="mt-2 text-[10px] text-slate-500">MP4, WebM, OGG, atau MOV · maks. {{ $videoMaxLabel ?? '120 MB' }}</p>
                            @error('video_file')
                                <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div x-show="videoMode === 'server'" x-cloak>
                    <label class="mb-1 block text-xs font-medium text-slate-600">Pilih file di server</label>
                    <select
                        name="video_path"
                        @change="onServerVideoChange($event)"
                        class="w-full rounded-xl border border-brand-200 px-3 py-2.5 text-sm"
                    >
                        <option value="">— Pilih file video —</option>
                        @foreach ($serverVideos as $serverVideo)
                            <option
                                value="{{ $serverVideo['path'] }}"
                                data-url="{{ $serverVideo['url'] }}"
                                @selected($defaultVideoPath === $serverVideo['path'])
                            >
                                {{ $serverVideo['name'] }} ({{ $serverVideo['size_label'] }})
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-2 text-[10px] text-slate-500">File dari folder <code class="rounded bg-slate-100 px-1">storage/app/public/article-videos</code> — tidak perlu upload ulang.</p>
                    @error('video_path')
                        <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                    <div x-show="serverVideoPreviewUrl" x-cloak class="mt-3 overflow-hidden rounded-2xl border border-violet-100 bg-black">
                        <video controls playsinline preload="metadata" class="aspect-video w-full" :src="serverVideoPreviewUrl"></video>
                    </div>
                </div>

                <div x-show="videoMode === 'link'" x-cloak>
                    <label class="mb-1 block text-xs font-medium text-slate-600">Link video (YouTube / Vimeo / MP4)</label>
                    <input
                        type="url"
                        name="video_link"
                        value="{{ $videoLink }}"
                        placeholder="https://www.youtube.com/watch?v=... atau https://vimeo.com/..."
                        class="w-full rounded-xl border border-brand-200 px-3 py-2.5 text-sm"
                    >
                    <p class="mt-2 text-[10px] text-slate-500">Contoh: YouTube, Vimeo, atau tautan langsung ke file .mp4</p>
                    @error('video_link')
                        <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                    @if ($article->videoEmbedUrl() && ! $article->hasStoredVideo())
                        <div class="mt-3 overflow-hidden rounded-2xl border border-violet-100 bg-black">
                            <iframe src="{{ $article->videoEmbedUrl() }}" title="Preview" class="aspect-video w-full" allowfullscreen></iframe>
                        </div>
                    @endif
                </div>

                @if ($article->videoPlaybackUrl())
                    <label class="mt-3 flex items-center gap-2 text-xs text-slate-600">
                        <input type="checkbox" name="remove_video" value="1" class="rounded border-brand-200 text-violet-600">
                        Hapus video saat ini
                    </label>
                @endif
            </div>

            <div>
                <label class="mb-2 block text-xs font-medium text-slate-600">Thumbnail video</label>
                <p class="mb-3 text-[10px] text-slate-500">Pilih frame dari video, atau unggah gambar thumbnail sendiri.</p>

                <div class="overflow-hidden rounded-2xl border border-dashed border-brand-200 bg-brand-50/40">
                    <div class="relative aspect-[16/9] bg-slate-100">
                        <img x-show="preview" x-cloak :src="preview" alt="Thumbnail preview" class="h-full w-full object-cover">
                        <div x-show="! preview && ! extracting" x-cloak class="flex h-full min-h-[9rem] flex-col items-center justify-center gap-2 text-slate-400">
                            <span class="text-3xl">▶</span>
                            <p class="text-[11px] font-medium">Thumbnail akan muncul setelah video diunggah atau gambar dipilih</p>
                        </div>
                        <div x-show="extracting" x-cloak class="flex h-full min-h-[9rem] flex-col items-center justify-center gap-2 text-slate-500">
                            <span class="h-8 w-8 animate-spin rounded-full border-2 border-brand-200 border-t-brand-600"></span>
                            <p class="text-[11px] font-medium">Mengambil frame terbaik dari video…</p>
                        </div>
                    </div>

                    <div x-show="thumbOptions.length > 0" x-cloak class="border-t border-brand-100 p-3">
                        <p class="mb-2 text-[10px] font-semibold uppercase tracking-wide text-slate-500">Dari video — pilih 1 frame</p>
                        <div class="grid grid-cols-3 gap-2">
                            <template x-for="(option, index) in thumbOptions" :key="'frame-' + index">
                                <button
                                    type="button"
                                    @click="selectFrame(index)"
                                    class="group relative overflow-hidden rounded-xl ring-2 transition"
                                    :class="thumbSource === 'frame' && selectedFrameIndex === index ? 'ring-violet-600' : 'ring-transparent hover:ring-violet-200'"
                                >
                                    <img :src="option.url" :alt="option.label" class="aspect-video w-full object-cover">
                                    <span
                                        class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 to-transparent px-1.5 py-1 text-[9px] font-semibold text-white"
                                        x-text="option.label"
                                    ></span>
                                    <span
                                        x-show="thumbSource === 'frame' && selectedFrameIndex === index"
                                        x-cloak
                                        class="absolute right-1 top-1 flex h-5 w-5 items-center justify-center rounded-full bg-violet-600 text-[10px] text-white"
                                    >✓</span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <div class="border-t border-brand-100 p-3">
                        <p class="mb-2 text-[10px] font-semibold uppercase tracking-wide text-slate-500">Atau unggah thumbnail</p>
                        <input
                            type="file"
                            accept="image/jpeg,image/png,image/webp"
                            x-ref="manualThumbInput"
                            @change="onManualThumbUpload($event)"
                            class="block w-full text-xs text-slate-600 file:mr-3 file:rounded-full file:border-0 file:bg-brand-600 file:px-4 file:py-2 file:text-xs file:font-semibold file:text-white hover:file:bg-brand-700"
                        >
                        <p class="mt-2 text-[10px] text-slate-500">JPG, PNG, atau WebP · maks. 2 MB · disarankan landscape 16:9</p>
                        <p x-show="thumbSource === 'upload'" x-cloak class="mt-2 text-[10px] font-semibold text-violet-700">✓ Thumbnail upload dipilih</p>
                    </div>
                </div>

                <input type="file" name="cover_image" accept="image/jpeg,image/png,image/webp" class="hidden" x-ref="coverInput">
                <button
                    type="button"
                    x-show="existingVideoUrl && ! extracting"
                    x-cloak
                    @click="generateThumbnails(videoObjectUrl || existingVideoUrl)"
                    class="mt-3 text-xs font-semibold text-violet-700 hover:text-violet-900"
                >
                    Ambil ulang thumbnail dari video
                </button>
                <p x-show="thumbError" x-cloak x-text="thumbError" class="mt-2 text-xs text-rose-600"></p>
                @error('cover_image')
                    <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
                @enderror

                @if ($article->coverImageUrl())
                    <label class="mt-3 flex items-center gap-2 text-xs text-slate-600">
                        <input type="checkbox" name="remove_cover_image" value="1" class="rounded border-brand-200 text-brand-600">
                        Hapus thumbnail saat ini (pilih frame baru dari video)
                    </label>
                @endif
            </div>
        @else
            <div>
                <label class="mb-2 block text-xs font-medium text-slate-600">Foto artikel</label>
                <div class="overflow-hidden rounded-2xl border border-dashed border-brand-200 bg-brand-50/40">
                    <div class="relative aspect-[16/9] bg-slate-100">
                        <img x-show="preview" x-cloak :src="preview" alt="" class="h-full w-full object-cover">
                        <div x-show="! preview" class="flex h-full min-h-[9rem] flex-col items-center justify-center gap-2 text-slate-400">
                            <svg class="h-10 w-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0 0 22.5 18.75V5.25A2.25 2.25 0 0 0 20.25 3H3.75A2.25 2.25 0 0 0 1.5 5.25v13.5A2.25 2.25 0 0 0 3.75 21Z"/></svg>
                            <p class="text-[11px] font-medium">Belum ada foto</p>
                        </div>
                    </div>
                    <div class="border-t border-brand-100 p-3">
                        <input
                            type="file"
                            name="cover_image"
                            accept="image/jpeg,image/png,image/webp"
                            @change="onCoverFileChange($event)"
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
        @endif

        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $article->is_published ?? true)) class="rounded border-brand-200 text-brand-600">
            <span>Publikasikan</span>
        </label>
        <button type="submit" class="w-full rounded-full bg-violet-600 py-3.5 text-sm font-semibold text-white shadow-soft">
            Simpan video
        </button>
    </form>
@endsection
