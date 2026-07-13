<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HealthArticle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class AdminArticleController extends Controller
{
    public function index(): View
    {
        $articles = HealthArticle::query()
            ->where('content_type', 'video')
            ->latest()
            ->paginate(15);

        return view('admin.articles.index', compact('articles'));
    }

    public function create(Request $request): View
    {
        $type = 'video';
        $article = new HealthArticle(['content_type' => $type]);

        $serverVideos = $this->serverVideoOptions();
        $videoMaxLabel = $this->videoMaxUploadLabel();

        return view('admin.articles.form', compact('article', 'type', 'serverVideos', 'videoMaxLabel'));
    }

    public function store(Request $request): RedirectResponse
    {
        $type = 'video';

        try {
            if ($uploadError = $this->resolveUploadError($request, $type)) {
                return back()->withInput()->with('upload_error', $uploadError);
            }

            $validated = $this->validateArticle($request, $type);
            $validated['slug'] = $this->resolveUniqueSlug($validated['title']);
            $validated['content_type'] = $type;

            if ($request->hasFile('cover_image')) {
                $validated['cover_image'] = $this->storeCoverImage($request->file('cover_image'));
            }

            if ($type === 'video') {
                $validated['excerpt'] = '';
                $validated['content'] = '';
                unset($validated['video_file'], $validated['video_source'], $validated['video_link'], $validated['video_path']);
                $validated['video_url'] = $this->resolveVideoUrl($request);
            }

            HealthArticle::create($validated);
        } catch (Throwable $e) {
            Log::error('Admin article store failed', [
                'type' => $type,
                'message' => $e->getMessage(),
            ]);

            return back()->withInput()->with(
                'upload_error',
                'Gagal menyimpan: '.$e->getMessage()
            );
        }

        return redirect()->route('admin.articles.index')->with('status', 'Video berhasil ditambahkan.');
    }

    public function edit(HealthArticle $article): View
    {
        abort_unless($article->isVideo(), 404);

        $type = 'video';

        $serverVideos = $this->serverVideoOptions();
        $videoMaxLabel = $this->videoMaxUploadLabel();

        return view('admin.articles.form', compact('article', 'type', 'serverVideos', 'videoMaxLabel'));
    }

    public function update(Request $request, HealthArticle $article): RedirectResponse
    {
        abort_unless($article->isVideo(), 404);

        $type = 'video';

        if ($uploadError = $this->resolveUploadError($request, $type, $article)) {
            return back()->withInput()->with('upload_error', $uploadError);
        }

        $validated = $this->validateArticle($request, $type, $article);
        $validated['content_type'] = $type;

        if (($validated['title'] ?? $article->title) !== $article->title) {
            $validated['slug'] = $this->resolveUniqueSlug($validated['title'], $article->id);
        }

        if ($request->boolean('remove_cover_image') && $article->cover_image) {
            Storage::disk('public')->delete($article->cover_image);
            $validated['cover_image'] = null;
        }

        if ($request->hasFile('cover_image')) {
            if ($article->cover_image) {
                Storage::disk('public')->delete($article->cover_image);
            }

            $validated['cover_image'] = $this->storeCoverImage($request->file('cover_image'));
        }

        if ($type === 'video') {
            $validated['excerpt'] = '';
            $validated['content'] = '';
            unset($validated['video_file'], $validated['video_source'], $validated['video_link'], $validated['video_path']);

            if ($request->boolean('remove_video') && $article->video_url) {
                $this->deleteVideoFile($article->video_url);
                $validated['video_url'] = null;
            } elseif ($request->input('video_source') === 'server' && $request->filled('video_path')) {
                $validated['video_url'] = $this->resolveServerVideoPath($request);
            } elseif ($request->input('video_source') === 'link' || $request->filled('video_link')) {
                if ($article->video_url && $article->hasStoredVideo()) {
                    $this->deleteVideoFile($article->video_url);
                }
                $validated['video_url'] = trim((string) $request->input('video_link'));
            } elseif ($request->hasFile('video_file')) {
                if ($article->video_url) {
                    $this->deleteVideoFile($article->video_url);
                }
                $validated['video_url'] = $this->storeVideoFile($request->file('video_file'));
            }
        }

        $article->update($validated);

        return redirect()->route('admin.articles.index')->with('status', 'Video berhasil diperbarui.');
    }

    public function destroy(HealthArticle $article): RedirectResponse
    {
        abort_unless($article->isVideo(), 404);
        if ($article->cover_image) {
            Storage::disk('public')->delete($article->cover_image);
        }

        if ($article->video_url) {
            $this->deleteVideoFile($article->video_url);
        }

        $article->delete();

        return redirect()->route('admin.articles.index')->with('status', 'Video berhasil dihapus.');
    }

    private function storeCoverImage(UploadedFile $file): string
    {
        return $file->store('article-covers', 'public');
    }

    private function storeVideoFile(UploadedFile $file): string
    {
        return $file->store('article-videos', 'public');
    }

    private function deleteVideoFile(string $path): void
    {
        if (! str_starts_with($path, 'http') && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function validateArticle(Request $request, string $type, ?HealthArticle $article = null): array
    {
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:50'],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'is_published' => ['sometimes', 'boolean'],
            'remove_cover_image' => ['sometimes', 'boolean'],
            'content_type' => ['required', 'in:article,video'],
        ];

        if ($type === 'video') {
            $hasExistingVideo = $article?->video_url && ! $request->boolean('remove_video');
            $videoSource = $request->input('video_source', 'file');
            $videoMaxKb = $this->videoMaxUploadKb();

            $rules['video_source'] = ['required', 'in:file,link,server'];
            $rules['remove_video'] = ['sometimes', 'boolean'];
            $rules['video_file'] = ['nullable', 'file', 'mimes:mp4,webm,ogg,quicktime,mov', 'max:'.$videoMaxKb];
            $rules['video_link'] = ['nullable', 'url', 'max:500'];
            $rules['video_path'] = ['nullable', 'string', 'max:255'];

            if ($videoSource === 'link') {
                $rules['video_link'] = [
                    $hasExistingVideo && ! $request->filled('video_link') ? 'nullable' : 'required',
                    'url',
                    'max:500',
                ];
            } elseif ($videoSource === 'server') {
                $allowedPaths = collect($this->serverVideoOptions())->pluck('path')->all();
                $rules['video_path'] = [
                    $hasExistingVideo && ! $request->filled('video_path') ? 'nullable' : 'required',
                    'string',
                    'max:255',
                    \Illuminate\Validation\Rule::in($allowedPaths),
                ];
            } else {
                $rules['video_file'] = [
                    $hasExistingVideo ? 'nullable' : 'required',
                    'file',
                    'mimes:mp4,webm,ogg,quicktime,mov',
                    'max:'.$videoMaxKb,
                ];
            }
        } else {
            $rules['content'] = ['required', 'string'];
        }

        return $request->validate($rules, [
            'cover_image.image' => 'File harus berupa gambar.',
            'cover_image.mimes' => 'Format foto: JPG, PNG, atau WebP.',
            'cover_image.max' => 'Ukuran foto maksimal 2 MB.',
            'video_file.required' => 'File video wajib diunggah, atau pilih opsi lain di bawah.',
            'video_file.mimes' => 'Format video: MP4, WebM, OGG, atau MOV.',
            'video_file.max' => 'Ukuran video maksimal '.$this->videoMaxUploadLabel().'.',
            'video_link.required' => 'Link video wajib diisi (YouTube, Vimeo, atau URL MP4).',
            'video_link.url' => 'Link video tidak valid.',
            'video_path.required' => 'Pilih file video yang sudah ada di server.',
            'video_path.in' => 'File video tidak ditemukan di folder article-videos.',
        ]) + ['is_published' => $request->boolean('is_published')];
    }

    private function resolveUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'konten';
        $slug = $base;
        $counter = 2;

        while (HealthArticle::query()
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    private function resolveVideoUrl(Request $request): string
    {
        if ($request->input('video_source') === 'link') {
            return trim((string) $request->input('video_link'));
        }

        if ($request->input('video_source') === 'server') {
            return $this->resolveServerVideoPath($request);
        }

        return $this->storeVideoFile($request->file('video_file'));
    }

    private function resolveServerVideoPath(Request $request): string
    {
        $path = trim((string) $request->input('video_path'));

        if ($path === '' || ! Storage::disk('public')->exists($path)) {
            throw new \InvalidArgumentException('File video tidak ditemukan di server.');
        }

        return $path;
    }

    /**
     * @return list<array{path: string, name: string, size: int, size_label: string, url: string}>
     */
    private function serverVideoOptions(): array
    {
        $disk = Storage::disk('public');

        if (! $disk->exists('article-videos')) {
            return [];
        }

        return collect($disk->files('article-videos'))
            ->filter(fn (string $path) => preg_match('/\.(mp4|webm|ogg|mov)$/i', $path))
            ->map(fn (string $path) => [
                'path' => $path,
                'name' => basename($path),
                'size' => $disk->size($path),
                'size_label' => $this->formatBytes($disk->size($path)),
                'url' => '/storage/'.$path,
            ])
            ->sortByDesc('size')
            ->values()
            ->all();
    }

    private function resolveUploadError(Request $request, string $type, ?HealthArticle $article = null): ?string
    {
        if ($type !== 'video' || in_array($request->input('video_source'), ['link', 'server'], true)) {
            return null;
        }

        $contentLength = (int) $request->server('CONTENT_LENGTH');
        $postMaxBytes = $this->iniBytes((string) ini_get('post_max_size'));
        if ($contentLength > 0 && $postMaxBytes > 0 && $contentLength > $postMaxBytes) {
            return 'Upload gagal: ukuran total melebihi post_max_size server ('.$this->formatBytes($postMaxBytes).'). Naikkan limit PHP di hosting, atau gunakan opsi Sudah di server / Link video.';
        }

        $hasExistingVideo = $article?->video_url && ! $request->boolean('remove_video');
        if ($hasExistingVideo) {
            return null;
        }

        $video = $request->file('video_file');
        if ($video && ! $video->isValid()) {
            return match ($video->getError()) {
                UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'Upload gagal: video terlalu besar untuk server (upload_max_filesize = '.$this->formatBytes($this->iniBytes((string) ini_get('upload_max_filesize'))).', batas aplikasi = '.$this->videoMaxUploadLabel().'). Naikkan limit PHP atau gunakan opsi Sudah di server / Link video.',
                UPLOAD_ERR_PARTIAL => 'Upload gagal: file video terputus. Coba lagi dengan koneksi stabil.',
                UPLOAD_ERR_NO_FILE => 'File video wajib diunggah.',
                default => 'Upload video gagal (kode error '.$video->getError().').',
            };
        }

        return null;
    }

    private function iniBytes(string $value): int
    {
        $value = trim($value);
        if ($value === '') {
            return 0;
        }

        $unit = strtolower(substr($value, -1));
        $number = (int) $value;

        return match ($unit) {
            'g' => $number * 1024 * 1024 * 1024,
            'm' => $number * 1024 * 1024,
            'k' => $number * 1024,
            default => (int) $value,
        };
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1024 * 1024) {
            return round($bytes / 1024 / 1024, 1).' MB';
        }

        if ($bytes >= 1024) {
            return round($bytes / 1024, 1).' KB';
        }

        return $bytes.' B';
    }

    private function videoMaxUploadKb(): int
    {
        return max(1, (int) config('education.video_max_upload_kb', 122880));
    }

    private function videoMaxUploadLabel(): string
    {
        return $this->formatBytes($this->videoMaxUploadKb() * 1024);
    }
}
