<?php

namespace App\Http\Controllers\Admin;

use App\Models\Article;
use Merlion\Http\Controllers\CrudController;

class ArticleController extends CrudController
{
    protected string $model = Article::class;
    protected string $route = 'articles';
    protected string $lang = 'article';

    protected function entities()
    {
        return [
            'actions' => ['view'],
            'columns' => [
                'title',
                'created_at',
            ],
            'filters' => [
                'title',
            ],
            'sorts'   => ['title', 'created_at'],
            'tools'   => [
                'create',
            ],
            'fields'  => [
                'title',
                [
                    'name'       => 'content',
                    'type'       => 'textarea',
                    'rows'       => 10,
                    'attributes' => [
                        'class' => 'w-100',
                    ],
                ],
            ],
        ];
    }
}
