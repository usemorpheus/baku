<?php

use App\Http\Controllers\Admin\AgentController;
use App\Http\Controllers\Admin\ArticleController;
use App\Models\Article;

Route::group([
    'prefix'     => 'admin',
    'as'         => 'admin.',
    'middleware' => 'merlion',
], function () {
    admin('admin')->routes();
    Route::group(['middleware' => 'merlion_auth'], function () {
        Route::get('/', function () {
            $json  = [
                'title'   => 'Dashboard',
                'back'    => 'admin/agents',
                'content' => [
                    [
                        'type'       => 'flex',
                        'gap'        => 1,
                        'alignItems' => 'end',
                        'content'    => [
                            [
                                'type'    => 'button',
                                'label'   => 'Button 1',
                                'primary' => true,
                                'icon'    => 'ri-add-line',
                            ],
                            [
                                'type'   => 'button',
                                'label'  => 'Button 2',
                                'danger' => '',
                                'icon'   => 'ri-add-line',
                            ],
                        ],
                    ],
                    [
                        'type'    => 'table',
                        'columns' => [
                            'id',
                            'title',
                            'created_at',
                        ],
                        'rows'    => Article::all(),
                    ],
                ],
            ];
            $admin = \Merlion\PageBuilder::build('admin', $json);
            return $admin->render();
        })->name('home');
        Route::resource('articles', ArticleController::class);
        Route::delete('articles/{article}', [ArticleController::class, 'destroy'])->name('articles.delete');
        Route::resource('agents', AgentController::class);
        Route::delete('agents/{agent}', [AgentController::class, 'destroy'])->name('agents.delete');
    });
});
