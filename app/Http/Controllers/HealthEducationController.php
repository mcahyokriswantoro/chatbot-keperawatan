<?php

namespace App\Http\Controllers;

use App\Models\AyosehatArticle;
use App\Models\HealthArticle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class HealthEducationController extends Controller
{
    public function index(): View
    {
        $categoryConfig = collect(config('ayosehat.categories'));
        $dbArticles = AyosehatArticle::query()
            ->active()
            ->orderByDesc('published_at')
            ->orderByDesc('synced_at')
            ->get();

        $articles = $this->mapArticlesForView($dbArticles, $categoryConfig);
        $categories = $this->buildCategoryGroups($articles, $categoryConfig);
        $lastSyncedAt = AyosehatArticle::query()->max('synced_at');

        return view('education.index', [
            'sourceUrl' => config('ayosehat.base_url'),
            'categories' => $categories,
            'articles' => $articles,
            'featured' => $articles->first(),
            'lastSyncedAt' => $lastSyncedAt,
        ]);
    }

    public function show(string $slug): RedirectResponse|View
    {
        $article = AyosehatArticle::query()
            ->active()
            ->where('external_slug', $slug)
            ->first();

        if ($article) {
            return redirect()->away($article->url);
        }

        $legacy = collect(config('ayosehat.categories', []))
            ->flatMap(fn (array $category) => $category['articles'] ?? [])
            ->firstWhere('slug', $slug);

        if ($legacy) {
            return redirect()->away($legacy['url']);
        }

        $article = HealthArticle::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        return view('education.show', compact('article'));
    }

    protected function mapArticlesForView(Collection $dbArticles, Collection $categoryConfig): Collection
    {
        return $dbArticles->map(function (AyosehatArticle $article) use ($categoryConfig) {
            $meta = $categoryConfig->firstWhere('id', $article->category_id) ?? [];

            return [
                'slug' => $article->external_slug,
                'title' => $article->title,
                'excerpt' => $article->excerpt,
                'url' => $article->url,
                'image' => $article->image_url,
                'read_min' => $article->read_min ?? 3,
                'tag' => $article->tag ?? $article->category_name,
                'category_id' => $article->category_id,
                'category_name' => $article->category_name,
                'category_icon' => $meta['icon'] ?? '📄',
                'category_gradient' => $meta['gradient'] ?? 'from-brand-500 to-blue-600',
                'category_chip' => $meta['chip'] ?? 'bg-brand-50 text-brand-700 ring-brand-200',
                'published_at' => $article->published_at?->translatedFormat('d M Y'),
            ];
        })->values();
    }

    protected function buildCategoryGroups(Collection $articles, Collection $categoryConfig): Collection
    {
        return $categoryConfig->map(function (array $category) use ($articles) {
            return array_merge($category, [
                'articles' => $articles->where('category_id', $category['id'])->values()->all(),
            ]);
        });
    }
}
