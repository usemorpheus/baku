<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Log;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramController
{
    /**
     * @throws TelegramSDKException
     */
    public function __invoke($token)
    {
        $updates = Telegram::getWebhookUpdate();
        $chat    = $updates->getChat();
        Telegram::sendMessage([
            'chat_id' => $chat->id,
            'text'    => 'Hello',
        ]);
        return 'ok';
    }
}
