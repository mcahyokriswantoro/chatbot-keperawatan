<?php

namespace App\Services;

use App\Models\AyosehatArticle;
use App\Models\HealthTip;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class HealthTipService
{
    public function currentWeekStart(?Carbon $reference = null): Carbon
    {
        return ($reference ?? now('Asia/Jakarta'))->copy()->startOfWeek(Carbon::MONDAY)->startOfDay();
    }

    /**
     * @return list<string>
     */
    public function getWeeklyTips(): array
    {
        $weekStart = $this->currentWeekStart()->toDateString();

        $tips = HealthTip::query()
            ->where('week_start', $weekStart)
            ->orderBy('sort_order')
            ->pluck('content')
            ->all();

        if ($tips !== []) {
            return $tips;
        }

        $latestWeek = HealthTip::query()->max('week_start');

        if ($latestWeek) {
            return HealthTip::query()
                ->where('week_start', $latestWeek)
                ->orderBy('sort_order')
                ->pluck('content')
                ->all();
        }

        return config('health.default_tips', []);
    }

    public function refreshFromArticles(?Carbon $weekStart = null): int
    {
        $weekStart = $weekStart ?? $this->currentWeekStart();
        $weekKey = $weekStart->toDateString();
        $limit = (int) config('health.tips_per_week', 6);

        $articles = AyosehatArticle::query()
            ->active()
            ->orderByDesc('published_at')
            ->orderByDesc('synced_at')
            ->limit($limit)
            ->get();

        if ($articles->isEmpty()) {
            return 0;
        }

        HealthTip::query()->where('week_start', $weekKey)->delete();

        foreach ($articles as $index => $article) {
            HealthTip::query()->create([
                'content' => $this->formatTip($article->excerpt, $article->title),
                'source_slug' => $article->external_slug,
                'source_url' => $article->url,
                'week_start' => $weekKey,
                'sort_order' => $index + 1,
            ]);
        }

        return $articles->count();
    }

    protected function formatTip(string $excerpt, string $title): string
    {
        $text = trim($excerpt) !== '' ? trim($excerpt) : trim($title);

        if (mb_strlen($text) <= 180) {
            return $text;
        }

        if (preg_match('/^(.+?[.!?])(\s|$)/u', $text, $matches)) {
            $sentence = trim($matches[1]);

            if (mb_strlen($sentence) >= 40) {
                return $sentence;
            }
        }

        return Str::limit($text, 177);
    }
}
