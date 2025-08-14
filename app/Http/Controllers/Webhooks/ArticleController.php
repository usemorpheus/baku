<?php

namespace App\Http\Controllers\Webhooks;

use App\Models\Article;
use Illuminate\Support\Str;

class ArticleController
{
    public function __invoke()
    {
        request()->validate([
            'title'   => 'required',
            'content' => 'required',
        ]);

        $article = Article::create([
            'uuid'      => Str::uuid(),
            'published' => false,
            'title'     => request('title'),
            'content'   => request('content'),
            'category'  => request('category'),
            'author'    => request('author'),
            'data'      => request('data'),
        ]);

        return route('news.show', $article->uuid);
    }
}
