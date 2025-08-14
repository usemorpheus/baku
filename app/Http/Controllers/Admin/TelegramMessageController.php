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
            'id',
            'chat_id',
            'user.first_name',
            'text',
            'datetime' => [
                'display' => function ($schema) {
                    return Carbon::createFromTimestamp($schema->getValue())->diffForHumans();
                },
            ],
        ];
    }
}
