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
            $form = Form::make()
                ->flex()->wrap()->gap(3)->alignItems('end')
                ->model(['name' => 'Alex']);
            $form->fields([
                \Merlion\Components\Form\Fields\Text::make()->name('name')->label('Name'),
                \Merlion\Components\Form\Fields\File::make()->name('image')->label('Image'),
                \Merlion\Components\Form\Fields\Button::make(icon: 'ri-add-line', color: "primary",
                    label: "Search"),
            ]);
            return admin()->content($form)->render();
        })->name('home');

        Route::post('/', function () {
            return request()->all();
        });
        Route::resource('articles', ArticleController::class);
        Route::delete('articles/{article}', [ArticleController::class, 'destroy'])->name('articles.delete');
        Route::resource('agents', AgentController::class);
        Route::delete('agents/{agent}', [AgentController::class, 'destroy'])->name('agents.delete');
    });
});
