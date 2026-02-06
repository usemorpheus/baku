<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::redirect('activity', 'activity/community');
Route::get('activity/community', [ActivityController::class, 'community']);
Route::get('activity/points', [ActivityController::class, 'points']);

// 任务相关路由 - 需要Telegram上下文
Route::middleware('web')->prefix('tasks')->name('tasks.')->group(function () {
    Route::get('/', [TaskController::class, 'index'])->name('index');
    Route::post('{taskId}/claim', [TaskController::class, 'claimTask'])->name('claim');
    Route::post('{userTaskId}/verify', [TaskController::class, 'submitVerification'])->name('verify');
    Route::get('points', [TaskController::class, 'getUserPoints'])->name('points');
    Route::get('verify-auth', [TaskController::class, 'verifyAuth'])->name('verify-auth');
    Route::get('{telegramUserId}/profile', [TaskController::class, 'userProfile'])->name('user-profile');
});

// 特定的Telegram任务路由，可以从Telegram bot链接直接访问
Route::get('telegram/tasks', [TaskController::class, 'index'])->name('telegram.tasks.index');

Route::resource('/articles', ArticleController::class);
Route::get('/articles/{uuid}/publish', [ArticleController::class, 'publish'])->name('articles.publish');

require __DIR__ . '/webhook.php';
require __DIR__ . '/admin.php';
