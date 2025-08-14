<?php

namespace App\Http\Controllers\Admin;

use App\Models\Article;
use Merlion\Http\Controllers\CrudController;

class ArticleController extends CrudController
{
    protected string $model = Article::class;

    protected function schemas(): array
    {
        return [
            'title'      => [
                'filterable' => true,
            ],
            'published'  => [
                'filterable' => true,
                'type'       => 'select',
                'exact'      => true,
                'options'    => [
                    0 => 'N',
                    1 => 'Y',
                ],
            ],
            'category',
            'content'    => [
                'type'     => 'editor',
                'hideFrom' => 'index',
                'full'     => true,
            ],
            'created_at' => [
                'showOn' => ['index', 'show'],
            ],
            'data'       => [
                'showOn' => 'show',
            ],
        ];
    }
}
