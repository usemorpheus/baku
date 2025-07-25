<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix'     => 'merchant',
    'as'         => 'merchant.',
    'middleware' => ['merlion:merchant'],
], function () {
    admin('merchant')->routes();
    Route::group(['middleware' => 'merlion_auth'], function () {
        Route::get('/', function () {
            return admin()->render();
        })->name('home');
        Route::get('debug', function () {
            return admin()->content(admin()->getId())->render();
        });
    });
});
