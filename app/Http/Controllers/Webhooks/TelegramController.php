<?php

namespace App\Http\Controllers\Webhooks;

use App\Models\TelegramChat;
use App\Models\TelegramMessage;
use App\Models\TelegramUser;
use Illuminate\Support\Carbon;

class TelegramController
{
    public function __invoke()
    {
        $data = request()->all();

        if (!empty($data['message']['text'])) {
            $message = $data['message'];

            if (!empty($message['chat'])) {
                $chat = TelegramChat::updateOrCreate([
                    'id' => $message['chat']['id'],
                ], [
                    'type'  => $message['chat']['type'] ?? 'private',
                    'title' => $message['chat']['title'] ?? $message['chat']['username'],
                ]);
            }

            if (!empty($message['from'])) {
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
                'telegram_chat_id' => $message['chat']['id'],
                'telegram_user_id' => $message['from']['id'] ?? null,
                'text'             => $message['text'] ?? null,
                'data'             => $message,
                'datetime'         => $message['date'],
            ]);
        }

        return $data;
    }

    public function getMessagesContext()
    {
        $chat_id    = request('chat_id');
        $from       = request('from', '-3 days');
        $created_at = Carbon::parse($from);

        $messages = TelegramMessage::where('telegram_chat_id', $chat_id)
            ->with(['user', 'chat'])
            ->where('created_at', '>=', $created_at)
            ->limit(1000)
            ->get();

        $result = '';
        foreach ($messages as $message) {
            $result .= $message->user->first_name . '(' . $message->user->username . ') [' . Carbon::createFromTimestamp($message->datetime)->toString() . ']:' . $message->text . "\n";
        }

        return [
            'context' => $result,
        ];
    }
}
