<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;

class ArticleController
{
    public function __invoke()
    {
        request()->validate([
            'title'   => 'required|min:3|max:255',
            'content' => 'required|min:3',
        ]);

        $article = Article::create([
            'title'     => request('title'),
            'content'   => request('content'),
            'published' => false,
        ]);

        return [
            'success' => true,
            'data'    => $article,
        ];
    }
}
