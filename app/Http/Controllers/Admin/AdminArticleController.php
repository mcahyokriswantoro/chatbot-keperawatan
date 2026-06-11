<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HealthArticle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminArticleController extends Controller
{
    public function index(): View
    {
        $articles = HealthArticle::latest()->paginate(15);

        return view('admin.articles.index', compact('articles'));
    }

    public function create(): View
    {
        return view('admin.articles.form', ['article' => new HealthArticle]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateArticle($request);
        $validated['slug'] = Str::slug($validated['title']);

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $this->storeCoverImage($request->file('cover_image'));
        }

        HealthArticle::create($validated);

        return redirect()->route('admin.articles.index')->with('status', 'Artikel berhasil ditambahkan.');
    }

    public function edit(HealthArticle $article): View
    {
        return view('admin.articles.form', compact('article'));
    }

    public function update(Request $request, HealthArticle $article): RedirectResponse
    {
        $validated = $this->validateArticle($request);

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

        $article->update($validated);

        return redirect()->route('admin.articles.index')->with('status', 'Artikel berhasil diperbarui.');
    }

    public function destroy(HealthArticle $article): RedirectResponse
    {
        if ($article->cover_image) {
            Storage::disk('public')->delete($article->cover_image);
        }

        $article->delete();

        return redirect()->route('admin.articles.index')->with('status', 'Artikel berhasil dihapus.');
    }

    private function storeCoverImage(UploadedFile $file): string
    {
        return $file->store('article-covers', 'public');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateArticle(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:50'],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'excerpt' => ['required', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'is_published' => ['sometimes', 'boolean'],
            'remove_cover_image' => ['sometimes', 'boolean'],
        ], [
            'cover_image.image' => 'File harus berupa gambar.',
            'cover_image.mimes' => 'Format foto: JPG, PNG, atau WebP.',
            'cover_image.max' => 'Ukuran foto maksimal 2 MB.',
        ]) + ['is_published' => $request->boolean('is_published')];
    }
}
