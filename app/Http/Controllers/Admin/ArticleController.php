<?php

namespace App\Http\Controllers\Admin;

use App\Models\Article;
use Merlion\Http\Controllers\CrudController;

class ArticleController extends CrudController
{
    protected string $model = Article::class;
    protected string $route = 'articles';
    protected string $lang = 'article';

    protected function schemas()
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
                [
                    'name'  => 'title',
                    'rules' => 'required|max:150',
                ],
                [
                    'name' => 'content',
                    'type' => 'textarea',
                    'full' => true,
                    'rows' => 10,
                ],
            ],
            'entries' => [
                [
                    'name'   => 'title',
                    'status' => 'success',
                ],
                [
                    'name' => 'content',
                    'full' => true,
                ],
            ],
        ];
    }
}
