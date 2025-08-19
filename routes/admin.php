<?php

use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TelegramChatController;
use App\Http\Controllers\Admin\TelegramMessageController;
use App\Http\Controllers\Admin\TelegramUserController;

Route::group([
    'prefix'     => config('merlion.admin.route.prefix'),
    'as'         => config('merlion.admin.route.as'),
    'middleware' => ['web', 'merlion:admin'],
], function () {
    admin()->authRoutes();
    Route::group(['middleware' => 'auth'], function () {
        Route::get('/', DashboardController::class);
        Route::resource('articles', ArticleController::class);
        Route::resource('telegram-chats', TelegramChatController::class);
        Route::resource('telegram-users', TelegramUserController::class);
        Route::resource('telegram-messages', TelegramMessageController::class);
        Route::resource('settings', SettingController::class);
    });
});
