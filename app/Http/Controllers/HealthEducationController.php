<?php

namespace App\Http\Controllers;

use App\Models\HealthArticle;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class HealthEducationController extends Controller
{
    public function index(): View
    {
        $articles = $this->mapArticlesForView($this->publishedArticles());
        $categories = $this->buildCategoriesForView($articles);

        return view('education.index', [
            'articles' => $articles,
            'categories' => $categories,
        ]);
    }

    public function show(string $slug): View
    {
        $article = HealthArticle::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->where('content_type', 'video')
            ->firstOrFail();

        return view('education.show', compact('article'));
    }

    /**
     * @return Collection<int, HealthArticle>
     */
    protected function publishedArticles(): Collection
    {
        if (! Schema::hasTable('health_articles')) {
            return collect();
        }

        return HealthArticle::query()
            ->where('is_published', true)
            ->where('content_type', 'video')
            ->latest()
            ->get();
    }

    /**
     * @param  Collection<int, HealthArticle>  $records
     * @return Collection<int, array<string, mixed>>
     */
    protected function mapArticlesForView(Collection $records): Collection
    {
        return $records->map(function (HealthArticle $article) {
            $categoryName = trim((string) $article->category) !== '' ? $article->category : 'Umum';
            $categoryId = 'cat-'.(Str::slug($categoryName) ?: 'umum');

            return [
                'slug' => $article->slug,
                'title' => $article->title,
                'url' => route('education.show', $article->slug),
                'image' => $article->coverImageUrl() ?? asset('images/robot.png'),
                'read_label' => 'Video edukasi',
                'tag' => $categoryName,
                'category_id' => $categoryId,
                'category_name' => $categoryName,
                'category_icon' => '▶️',
                'category_chip' => 'bg-violet-50 text-violet-700 ring-violet-200',
                'published_at' => $article->created_at?->translatedFormat('d M Y'),
                'is_video' => true,
            ];
        })->values();
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $articles
     * @return Collection<int, array<string, mixed>>
     */
    protected function buildCategoriesForView(Collection $articles): Collection
    {
        return $articles->unique('category_id')->map(fn (array $article) => [
            'id' => $article['category_id'],
            'name' => $article['category_name'],
            'icon' => '📂',
            'chip' => 'bg-slate-50 text-slate-700 ring-slate-200',
        ])->values();
    }
}
