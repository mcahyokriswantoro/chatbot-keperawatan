<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HealthArticle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        $article->update($validated);

        return redirect()->route('admin.articles.index')->with('status', 'Artikel berhasil diperbarui.');
    }

    public function destroy(HealthArticle $article): RedirectResponse
    {
        $article->delete();

        return redirect()->route('admin.articles.index')->with('status', 'Artikel berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateArticle(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:50'],
            'excerpt' => ['required', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'is_published' => ['sometimes', 'boolean'],
        ]) + ['is_published' => $request->boolean('is_published')];
    }
}
