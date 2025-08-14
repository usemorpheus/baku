<?php

namespace App\Http\Controllers\Webhooks;

use App\Models\TelegramChat;
use App\Models\TelegramMessage;
use App\Models\TelegramUser;

class TelegramController
{
    public function __invoke()
    {
        $data = request()->all();

        if (!empty($data['message'])) {
            $message = $data['message'];

            if (empty($message['chat'])) {
                $chat = TelegramChat::updateOrCreate([
                    'id' => $message['chat']['id'],
                ], [
                    'type'  => $message['chat']['type'] ?? 'private',
                    'title' => $message['chat']['title'] ?? $message['chat']['username'],
                ]);
            }

            if (empty($message['from'])) {
                $user = TelegramUser::updateOrCreate([
                    'id'     => $message['from']['id'],
                    'is_bot' => $message['from']['is_bot'] ?? false,
                ], [
                    'first_name' => $message['from']['first_name'],
                    'username'   => $message['from']['username'],
                ]);
            }

            if (!empty($chat) && !empty($user)) {
                $chat->users()->syncWithoutDetaching([
                    $user->id,
                ]);
            }

            TelegramMessage::updateOrCreate([
                'message_id' => $message['message_id'],
            ], [
                'telegram_user_id' => $message['from']['id'] ?? null,
                'text'             => $message['text'] ?? null,
                'data'             => $message,
            ]);
        }

        return [
            'success' => true,
        ];
    }
}
