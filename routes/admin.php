<?php

use App\Http\Controllers\Admin\AgentController;
use App\Http\Controllers\Admin\ArticleController;
use Merlion\Components\Pages\Home;

Route::group([
    'prefix'     => 'admin',
    'as'         => 'admin.',
    'middleware' => 'merlion',
], function () {
    admin('admin')->routes();
    Route::group(['middleware' => 'merlion_auth'], function () {
        Route::get('/', function () {
            \Illuminate\Support\Facades\Log::debug('1');
            return admin()->content(Home::make())->render();
        })->name('home');
        Route::resource('articles', ArticleController::class);
        Route::delete('articles/{article}', [ArticleController::class, 'destroy'])->name('articles.delete');
        Route::resource('agents', AgentController::class);
        Route::delete('agents/{agent}', [AgentController::class, 'destroy'])->name('agents.delete');
    });
});
