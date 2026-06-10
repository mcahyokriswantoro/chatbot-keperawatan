<?php

namespace App\Services;

use App\Models\AyosehatArticle;
use DOMDocument;
use DOMXPath;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AyosehatSyncService
{
    /**
     * @return array{synced: int, deactivated: int, errors: list<string>}
     */
    public function sync(): array
    {
        $baseUrl = rtrim(config('ayosehat.base_url'), '/');
        $categories = config('ayosehat.categories', []);
        $syncedSlugs = [];
        $errors = [];
        $syncedAt = now();

        foreach ($categories as $category) {
            $slug = $category['slug'] ?? null;
            if (! $slug) {
                continue;
            }

            $url = "{$baseUrl}/category/{$slug}";
            $html = $this->fetch($url);

            if ($html === null) {
                $errors[] = "Gagal mengambil kategori {$category['name']} ({$url})";

                continue;
            }

            $articles = $this->parseCategoryPage($html, $category, $baseUrl);

            foreach ($articles as $article) {
                AyosehatArticle::query()->updateOrCreate(
                    ['external_slug' => $article['external_slug']],
                    array_merge($article, [
                        'is_active' => true,
                        'synced_at' => $syncedAt,
                    ]),
                );

                $syncedSlugs[] = $article['external_slug'];
            }
        }

        $categoryIds = collect($categories)->pluck('id')->filter()->all();

        $deactivated = 0;
        if ($syncedSlugs !== []) {
            $deactivated = AyosehatArticle::query()
                ->whereIn('category_id', $categoryIds)
                ->whereNotIn('external_slug', $syncedSlugs)
                ->where('is_active', true)
                ->update(['is_active' => false]);
        }

        return [
            'synced' => count($syncedSlugs),
            'deactivated' => $deactivated,
            'errors' => $errors,
        ];
    }

    protected function fetch(string $url): ?string
    {
        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'User-Agent' => 'ChatbotKeperawatan/1.0 (+https://ayosehat.kemkes.go.id)',
                    'Accept' => 'text/html',
                ])
                ->get($url);

            if (! $response->successful()) {
                return null;
            }

            return $response->body();
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * @param  array{id: string, name: string, slug: string, icon?: string, gradient?: string, chip?: string}  $category
     * @return list<array<string, mixed>>
     */
    public function parseCategoryPage(string $html, array $category, string $baseUrl): array
    {
        $dom = new DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);
        $items = $xpath->query("//div[contains(@class,'article-list')]//div[contains(@class,'item')]");
        $articles = [];

        if ($items === false) {
            return [];
        }

        foreach ($items as $item) {
            $parsed = $this->parseItem($xpath, $item, $category, $baseUrl);
            if ($parsed !== null) {
                $articles[$parsed['external_slug']] = $parsed;
            }
        }

        return array_values($articles);
    }

    /**
     * @param  array{id: string, name: string}  $category
     * @return array<string, mixed>|null
     */
    protected function parseItem(DOMXPath $xpath, \DOMNode $item, array $category, string $baseUrl): ?array
    {
        $linkNode = $xpath->query(".//a[contains(@class,'link')]", $item)->item(0);
        $titleNode = $xpath->query(".//h4[contains(@class,'article-title')]", $item)->item(0);

        if (! $linkNode instanceof \DOMElement || ! $titleNode) {
            return null;
        }

        $href = trim($linkNode->getAttribute('href'));
        $title = trim(preg_replace('/\s+/', ' ', $titleNode->textContent ?? '') ?? '');

        if ($href === '' || $title === '') {
            return null;
        }

        $url = $this->normalizeUrl($href, $baseUrl);
        $externalSlug = $this->slugFromUrl($url, $baseUrl);

        if ($externalSlug === '') {
            return null;
        }

        $excerpt = '';
        $paragraphs = $xpath->query('.//p', $item);
        if ($paragraphs !== false) {
            foreach ($paragraphs as $paragraph) {
                $text = trim(preg_replace('/\s+/', ' ', $paragraph->textContent ?? '') ?? '');
                if ($text !== '') {
                    $excerpt = Str::limit($text, 220);
                    break;
                }
            }
        }

        $imageUrl = null;
        $imgNode = $xpath->query('.//img', $item)->item(0);
        if ($imgNode instanceof \DOMElement) {
            $src = trim($imgNode->getAttribute('src'));
            if ($src !== '') {
                $imageUrl = $this->normalizeUrl($src, $baseUrl);
            }
        }

        $readMin = null;
        $publishedAt = null;
        $tag = null;

        $metaNodes = $xpath->query(".//div[contains(@class,'article-meta')]//div", $item);
        if ($metaNodes !== false) {
            foreach ($metaNodes as $metaNode) {
                $metaText = trim(preg_replace('/\s+/', ' ', $metaNode->textContent ?? '') ?? '');

                if (preg_match('/(\d+)\s*Menit/i', $metaText, $matches)) {
                    $readMin = (int) $matches[1];
                } elseif ($publishedAt === null) {
                    try {
                        $publishedAt = Carbon::parse($metaText)->toDateString();
                    } catch (\Throwable) {
                        // ignore unparsable dates
                    }
                }
            }
        }

        $catNode = $xpath->query(".//div[contains(@class,'article-cat')]//a", $item)->item(0);
        if ($catNode instanceof \DOMElement) {
            $tag = Str::limit(trim($catNode->textContent ?? ''), 40) ?: null;
        }

        if ($tag === null) {
            $tag = $category['name'];
        }

        return [
            'external_slug' => $externalSlug,
            'category_id' => $category['id'],
            'category_name' => $category['name'],
            'title' => $title,
            'excerpt' => $excerpt !== '' ? $excerpt : Str::limit($title, 120),
            'url' => $url,
            'image_url' => $imageUrl,
            'tag' => $tag,
            'read_min' => $readMin,
            'published_at' => $publishedAt,
        ];
    }

    protected function normalizeUrl(string $url, string $baseUrl): string
    {
        if (str_starts_with($url, '//')) {
            return 'https:'.$url;
        }

        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }

        if (str_starts_with($url, '/')) {
            return $baseUrl.$url;
        }

        return $baseUrl.'/'.$url;
    }

    protected function slugFromUrl(string $url, string $baseUrl): string
    {
        $path = parse_url($url, PHP_URL_PATH);
        $path = trim((string) $path, '/');

        if (str_starts_with($path, 'category/')) {
            return '';
        }

        return urldecode($path);
    }
}
