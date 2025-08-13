<?php

use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\DashboardController;

Route::group([
    'prefix'     => config('merlion.admin.route.prefix'),
    'as'         => config('merlion.admin.route.as'),
    'middleware' => ['web', 'merlion:admin'],
], function () {
    admin()->authRoutes();
    Route::group(['middleware' => 'auth'], function () {
        Route::get('/', DashboardController::class);
        Route::resource('articles', ArticleController::class);
    });
});
