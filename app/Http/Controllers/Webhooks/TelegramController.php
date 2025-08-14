<?php

namespace App\Http\Controllers\Webhooks;

class TelegramController
{
    public function __invoke()
    {
        return request()->all();
    }
}
