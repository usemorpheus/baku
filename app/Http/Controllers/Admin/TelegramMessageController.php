<?php

namespace App\Http\Controllers\Admin;

use App\Models\TelegramMessage;
use Carbon\Carbon;
use Merlion\Http\Controllers\CrudController;

class TelegramMessageController extends CrudController
{
    protected string $model = TelegramMessage::class;
    protected string $route = 'telegram-messages';

    protected function schemas(): array
    {
        return [
            'message_id',
            'telegram_chat_id',
            'telegram_user_id',
            'text',
            'datetime' => [
                'display' => function ($schema) {
                    return Carbon::createFromTimestamp($schema->getValue())->diffForHumans();
                },
            ],
        ];
    }
}
