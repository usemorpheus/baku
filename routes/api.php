<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\TelegramController;
use Illuminate\Support\Facades\Route;

Route::post('articles', ArticleController::class);

Route::any('telegram/{code}/webhook', TelegramController::class);


