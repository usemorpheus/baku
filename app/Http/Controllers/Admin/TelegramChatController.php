<?php

namespace App\Http\Controllers\Admin;

use App\Models\TelegramChat;
use Merlion\Http\Controllers\CrudController;

class TelegramChatController extends CrudController
{
    protected string $model = TelegramChat::class;
    protected string $route = 'telegram-chats';

    protected function schemas(): array
    {
        return [
            'id',
            'title' => [
                'filterable' => true,
            ],
            'type'  => [
                'type'    => 'select',
                'options' => [
                    'private'    => 'Private',
                    'group'      => 'Group',
                    'supergroup' => 'Supergroup',
                ],
            ],
        ];
    }
}
