<?php

use App\Http\Controllers\Admin\AgentController;
use App\Http\Controllers\Admin\ArticleController;
use Merlion\Components\Form\Form;

Route::group([
    'prefix'     => 'admin',
    'as'         => 'admin.',
    'middleware' => 'merlion',
], function () {
    admin('admin')->routes();
    Route::group(['middleware' => 'merlion_auth'], function () {
        Route::get('/', function () {
            $form = Form::make()->flex()->wrap()->gap(3);
            $form->fields([
                \Merlion\Components\Form\Fields\Text::make()->name('name')->label('Name'),
                \Merlion\Components\Form\Fields\Space::make()->class('w-full'),
                \Merlion\Components\Form\Fields\File::make()->name('image')->label('Image'),
            ]);
            return admin()->content($form)->render();

            $json    = [
                'type'       => 'flex',
                'gap'        => 1,
                'column'     => true,
                'alignItems' => 'start',
                'content'    => [
                    'type'    => 'form',
                    'content' => [
                        [
                            'type'  => 'fields.text',
                            'name'  => 'name',
                            'label' => '姓名',
                        ],
                    ],
                ],
            ];
            $content = \Merlion\PageBuilder::build($json);
            return admin()->content($content)->render();
        })->name('home');
        Route::resource('articles', ArticleController::class);
        Route::delete('articles/{article}', [ArticleController::class, 'destroy'])->name('articles.delete');
        Route::resource('agents', AgentController::class);
        Route::delete('agents/{agent}', [AgentController::class, 'destroy'])->name('agents.delete');
    });
});
