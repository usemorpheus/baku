<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class);
Route::redirect('activity', 'activity/community');
Route::get('activity/community', [ActivityController::class, 'community']);
Route::get('activity/points', [ActivityController::class, 'points']);
Route::resource('/articles', ArticleController::class);
Route::get('/articles/{uuid}/publish', [ArticleController::class, 'publish'])->name('articles.publish');

require __DIR__ . '/webhook.php';
require __DIR__ . '/admin.php';
