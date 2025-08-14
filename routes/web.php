<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class);
Route::resource('/news', ArticleController::class);
Route::get('/news/{uuid}/publish', [ArticleController::class, 'publish'])->name('news.publish');

require __DIR__ . '/webhook.php';
require __DIR__ . '/admin.php';
