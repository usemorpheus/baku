<?php

namespace App\Http\Controllers;

use App\Models\Article;

class ArticleController
{
    public function index()
    {
        return view('articles.index', [
            'models' => Article::published()->latest()->paginate(),
        ]);
    }

    public function show(Article $article)
    {
        return view('articles.show', ['model' => $article]);
    }
}
