<?php

use App\Http\Controllers\Admin\AgentController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\DashboardController;
use Merlion\Components\Form\Errors;

Route::group([
    'prefix'     => 'admin',
    'as'         => 'admin.',
    'middleware' => ['web', 'merlion'],
], function () {
    admin('admin')->routes();
    Route::group(['middleware' => 'merlion_auth'], function () {
        Route::get('/', DashboardController::class)->name('home');
        Route::resource('articles', ArticleController::class);
        Route::delete('articles/{article}', [ArticleController::class, 'destroy'])->name('articles.delete');
        Route::resource('agents', AgentController::class);
        Route::delete('agents/{agent}', [AgentController::class, 'destroy'])->name('agents.delete');
    });
});
