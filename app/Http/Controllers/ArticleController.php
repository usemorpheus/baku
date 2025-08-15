<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Support\Facades\Http;

class ArticleController
{
    public function index()
    {
        $builder = Article::published()->latest();

        if ($category = request('category')) {
            $builder->where('category', $category);
        }

        $articles = $builder->paginate(10);

        return view('articles.index', compact('articles'));
    }

    public function show($uuid)
    {
        $article = Article::firstWhere('uuid', $uuid);
        $article->increment('read_count');
        return view('articles.show', compact('article'));
    }

    public function publish($uuid)
    {
        $article = Article::firstWhere('uuid', $uuid);
        $article->update(['published' => 1]);

        $data = $article->data;
        if (!empty($data['chat_id'])) {
            Http::post('https://n8n.baku.builders/webhook/f5b3f27a-0578-4556-996e-4006a92ac5b8',
                ['chat_id' => $data['chat_id']]);
        }
        return back();
    }
}
