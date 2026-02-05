<?php

use App\Http\Controllers\Webhooks\ArticleController;
use App\Http\Controllers\Webhooks\SettingController;
use App\Http\Controllers\Webhooks\TelegramController;
use App\Http\Controllers\Webhooks\ZealyController;
use App\Http\Middleware\ApiVerifyMiddleware;
use Illuminate\Support\Facades\Route;

Route::post('webhook/telegram', TelegramController::class);
Route::post('webhook/telegram-tasks', [TelegramController::class, 'handleTaskUpdates']);
Route::post('webhook/zealy', ZealyController::class);

Route::group(['middleware' => [ApiVerifyMiddleware::class]], function () {
    Route::post('webhook/articles', ArticleController::class);
    Route::get('webhook/messages', [TelegramController::class, 'getMessagesContext']);
    Route::post('webhook/messages', [TelegramController::class, 'saveMessage']);
    Route::post('webhook/add-baku', [TelegramController::class, 'addBaku']);
    Route::post('webhook/save-baku-community-data', [TelegramController::class, 'saveBakuCommunityData']);
    Route::post('webhook/update-baku-market-data', [TelegramController::class, 'updateBakuMarketData']);
    Route::post('webhook/update-baku-index-data', [TelegramController::class, 'updateBakuIndexData']);
    Route::get('webhook/get-baku-metric-data', [TelegramController::class, 'getMetricData']);
    Route::post('webhook/verify-task', [ZealyController::class, 'verifyTask']);
    Route::get('webhook/chats', [TelegramController::class, 'getGroupChats']);
    Route::get('webhook/setting/{key}', [SettingController::class, 'show']);
    Route::post('webhook/setting/{key}', [SettingController::class, 'update']);
    Route::post('webhook/calculate-rankings', [TelegramController::class, 'calculateRankings']);
});
