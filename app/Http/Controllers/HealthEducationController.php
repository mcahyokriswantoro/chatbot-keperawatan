<?php

namespace App\Http\Controllers;

use App\Models\HealthArticle;
use Illuminate\View\View;

class HealthEducationController extends Controller
{
    public function index(): View
    {
        $articles = HealthArticle::query()
            ->where('is_published', true)
            ->latest()
            ->paginate(10);

        return view('education.index', compact('articles'));
    }

    public function show(string $slug): View
    {
        $article = HealthArticle::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        return view('education.show', compact('article'));
    }
}
