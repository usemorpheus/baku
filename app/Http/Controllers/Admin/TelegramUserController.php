<?php

namespace App\Http\Controllers\Admin;

use App\Models\TelegramUser;
use Merlion\Http\Controllers\CrudController;

class TelegramUserController extends CrudController
{
    protected string $model = TelegramUser::class;
    protected string $route = 'telegram-users';

    protected function schemas(): array
    {
        return [
            'id',
            'username'   => [
                'filterable' => true,
            ],
            'first_name' => [
                'filterable' => true,
            ],
        ];
    }
}
