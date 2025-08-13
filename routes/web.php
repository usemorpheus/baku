<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class);
Route::resource('/news', ArticleController::class);

require __DIR__ . '/admin.php';
