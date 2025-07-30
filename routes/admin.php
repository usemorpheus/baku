<?php

use App\Http\Controllers\Admin\AgentController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DynamicImageController;
use App\Http\Controllers\Admin\SocialAccountController;
use App\Http\Controllers\Admin\TwitterController;

admin('admin')->routes();

admin()->routeAuthedGroup(function () {
    Route::get('image', [DynamicImageController::class, 'index']);
    Route::get('/', DashboardController::class)->name('home');
    Route::get('twitter/login', [TwitterController::class, 'login'])->name('twitter.login');
    Route::get('twitter/callback', [TwitterController::class, 'callback'])->name('twitter.callback');
    Route::get('twitter/refresh', [TwitterController::class, 'refresh'])->name('twitter.refresh');
    Route::resource('articles', ArticleController::class);
    Route::resource('agents', AgentController::class);
    Route::resource('social_accounts', SocialAccountController::class);
});
