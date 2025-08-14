<?php

use App\Http\Controllers\Webhooks\TelegramController;
use Illuminate\Support\Facades\Route;

Route::post('webhook/telegram', TelegramController::class);
