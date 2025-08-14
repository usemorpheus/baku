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
                'sortable'   => [
                    'title'  => 'A - Z',
                    '-title' => 'Z - A',
                ],
            ],
            'uuid'       => [
                'showOn' => 'show',
                'link'   => function ($text) {
                    return '/news/' . $text->getModel()->uuid;
                },
                'target' => '_blank',
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
            'category'   => [
                'type'       => 'select',
                'filterable' => true,
                'options'    => [
                    'private_interview' => 'Private Interview',
                    'group_report'      => 'Group report',
                    'buzz_news'         => 'Buzz news',
                ],
            ],
            'content'    => [
                'type'     => 'editor',
                'hideFrom' => 'index',
                'full'     => true,
            ],
            'created_at' => [
                'showOn'   => ['index', 'show'],
                'sortable' => [
                    'created_at'  => 'Oldest',
                    '-created_at' => [
                        'label'   => 'Newest',
                        'default' => true,
                    ],
                ],
            ],
            'data'       => [
                'showOn' => 'show',
            ],
        ];
    }
}
