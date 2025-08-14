<?php

namespace App\Http\Controllers\Webhooks;

use App\Actions\GenerateImage;
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
            'data'      => to_json(request('data')),
        ]);

        if ($article->category == 'buzz_news') {
            $image = GenerateImage::run($article->title, $article->content);
            $article->update([
                'image' => $image,
            ]);
        }

        return [
            'article' => $article,
            'url'     => route('articles.show', $article->uuid),
        ];
    }
}
