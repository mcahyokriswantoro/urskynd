<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::latest('published_at');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        $articles = $query->paginate(10)->withQueryString();
        $categories = Article::select('category')->distinct()->pluck('category');

        return view('admin.articles.index', compact('articles', 'categories'));
    }

    public function create()
    {
        return view('admin.articles.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'excerpt' => 'required|string|max:500',
            'content' => 'required|string',
            'read_time' => 'required|string|max:50',
            'author_name' => 'required|string|max:100',
            'author_role' => 'nullable|string|max:100',
            'image' => 'nullable|string|max:500',
            'is_published' => 'nullable|boolean',
        ]);

        $validated['slug'] = Str::slug($validated['title']);
        $originalSlug = $validated['slug'];
        $counter = 1;
        while (Article::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = "{$originalSlug}-{$counter}";
            $counter++;
        }

        if ($request->boolean('is_published', true)) {
            $validated['published_at'] = now();
        } else {
            $validated['published_at'] = null;
        }

        Article::create($validated);

        return redirect()->route('admin.articles.index')->with('success', 'Artikel berita berhasil diterbitkan!');
    }

    public function edit(Article $article)
    {
        return view('admin.articles.edit', compact('article'));
    }

    public function update(Request $request, Article $article)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'excerpt' => 'required|string|max:500',
            'content' => 'required|string',
            'read_time' => 'required|string|max:50',
            'author_name' => 'required|string|max:100',
            'author_role' => 'nullable|string|max:100',
            'image' => 'nullable|string|max:500',
            'is_published' => 'nullable|boolean',
        ]);

        if ($article->title !== $validated['title']) {
            $newSlug = Str::slug($validated['title']);
            $originalSlug = $newSlug;
            $counter = 1;
            while (Article::where('slug', $newSlug)->where('id', '!=', $article->id)->exists()) {
                $newSlug = "{$originalSlug}-{$counter}";
                $counter++;
            }
            $validated['slug'] = $newSlug;
        }

        if ($request->boolean('is_published', true)) {
            $validated['published_at'] = $article->published_at ?? now();
        } else {
            $validated['published_at'] = null;
        }

        $article->update($validated);

        return redirect()->route('admin.articles.index')->with('success', 'Artikel berita berhasil diperbarui!');
    }

    public function destroy(Article $article)
    {
        $article->delete();
        return redirect()->route('admin.articles.index')->with('success', 'Artikel berita berhasil dihapus.');
    }
}
