<?php

use App\Http\Controllers\Admin\AgentController;
use App\Http\Controllers\Admin\ArticleController;
use Illuminate\Support\Facades\Route;
use Merlion\Components\Card;
use Merlion\Components\Container;
use Merlion\Http\Middleware\Authenticate;
use Merlion\Http\Middleware\MerlionMiddleware;

Route::get('/', function () {
    return view('welcome');
});

Route::group([
    'prefix'     => 'admin',
    'as'         => 'admin.',
    'middleware' => [MerlionMiddleware::class, Authenticate::class],
], function () {
    Route::resource('articles', ArticleController::class);
    Route::delete('articles/{article}', [ArticleController::class, 'destroy'])->name('articles.delete');
    Route::resource('agents', AgentController::class);

    Route::get('debug', function () {
        $debug = Container::make()->setView('debug')->set('foo', 'bar');
        $debug->content(Card::make());
        return admin()->content($debug)->render();
    });
});
