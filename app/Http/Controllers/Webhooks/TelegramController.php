<?php

namespace App\Http\Controllers\Webhooks;

class TelegramController
{
    public function __invoke()
    {
        return [
            'success' => true,
        ];
    }
}
