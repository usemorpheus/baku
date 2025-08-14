<?php

namespace App\Http\Controllers\Admin;

use App\Models\TelegramMessage;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Merlion\Http\Controllers\CrudController;

class TelegramMessageController extends CrudController
{
    protected string $model = TelegramMessage::class;
    protected string $route = 'telegram-messages';

    protected function getQueryBuilder(): Builder
    {
        return TelegramMessage::query()->with(['chat', 'user']);
    }

    protected function schemas(): array
    {
        return [
            'message_id',
            'chat.title'      => [
                'hideFrom' => ['create', 'edit'],
            ],
            'user.first_name' => [
                'hideFrom' => ['create', 'edit'],
            ],
            'telegram_user_id',
            'text',
            'datetime'        => [
                'getValueUsing' => function ($schema) {
                    return Carbon::createFromTimestamp($schema->getModel()->datetime)->diffForHumans();
                },
            ],
        ];
    }
}
