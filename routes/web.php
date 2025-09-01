<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class);
Route::get('activity', [HomeController::class, 'activity']);
Route::resource('/articles', ArticleController::class);
Route::get('/articles/{uuid}/publish', [ArticleController::class, 'publish'])->name('articles.publish');

require __DIR__ . '/webhook.php';
require __DIR__ . '/admin.php';
