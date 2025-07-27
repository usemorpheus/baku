<?php

use App\Http\Controllers\Api\TelegramController;
use Illuminate\Support\Facades\Route;

Route::any('telegram/{token}/webhook', TelegramController::class);
