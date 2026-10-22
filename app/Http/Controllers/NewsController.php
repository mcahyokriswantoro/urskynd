<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    /**
     * Display a listing of skincare articles & news.
     */
    public function index(Request $request)
    {
        $query = Article::published();

        // Filter by category
        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $categories = [
            'Tips & Perawatan',
            'Kandungan Aktif',
            'Edukasi Kulit',
            'Tren & Riset',
        ];

        $currentCategory = $request->category ?? 'all';

        // Featured article is the first published one if no search/filter
        $featuredArticle = null;
        if ($currentCategory === 'all' && !$request->filled('search')) {
            $featuredArticle = (clone $query)->first();
            if ($featuredArticle) {
                $query->where('id', '!=', $featuredArticle->id);
            }
        }

        $articles = $query->paginate(9)->withQueryString();

        return view('news.index', compact('articles', 'featuredArticle', 'categories', 'currentCategory'));
    }

    /**
     * Display the specified news article.
     */
    public function show(string $slug)
    {
        $article = Article::published()->where('slug', $slug)->firstOrFail();

        $relatedArticles = Article::published()
            ->where('id', '!=', $article->id)
            ->where('category', $article->category)
            ->take(3)
            ->get();

        if ($relatedArticles->isEmpty()) {
            $relatedArticles = Article::published()
                ->where('id', '!=', $article->id)
                ->take(3)
                ->get();
        }

        return view('news.show', compact('article', 'relatedArticles'));
    }
}
