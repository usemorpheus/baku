<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Article;

class ArticleController
{
    public function index()
    {
        $articles = Article::published()->latest()->paginate(10);
        return view('news.index', compact('articles'));
    }

    public function show($id)
    {
        $article = Article::findOrFail($id);
        $article->increment('read_count');
        return view('news.show', compact('article'));
    }
}
