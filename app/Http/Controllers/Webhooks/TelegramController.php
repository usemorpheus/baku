<?php

namespace App\Http\Controllers\Webhooks;

use App\Models\TelegramChat;
use App\Models\TelegramMessage;
use App\Models\TelegramUser;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class TelegramController
{
    public function __invoke()
    {
        $data = request()->all();

        Log::debug($data);

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

        if (!empty($data['my_chat_member'])) {
            $message = $data['my_chat_member'];
            if (!empty($message['from'])) {
                $user = TelegramUser::updateOrCreate([
                    'id'     => $message['from']['id'],
                    'is_bot' => $message['from']['is_bot'] ?? false,
                ], [
                    'first_name' => $message['from']['first_name'],
                    'username'   => $message['from']['username'],
                ]);
            }
            if (!empty($message['chat'])) {
                $chat = TelegramChat::updateOrCreate([
                    'id' => $message['chat']['id'],
                ], [
                    'type'  => $message['chat']['type'] ?? 'private',
                    'title' => $message['chat']['title'] ?? $message['chat']['username'],
                ]);
            }
        }

        if (!empty($chat) && $chat->wasRecentlyCreated && !empty($user)) {
            $chat->setMeta('invit_by', $user->id);
        }

        return $data;
    }

    public function getMessagesContext()
    {
        $chat_id = request('chat_id');
        $chat    = TelegramChat::findOrFail($chat_id);

        $from       = request('from', '-3 days');
        $created_at = Carbon::parse($from);

        $messages = TelegramMessage::where('telegram_chat_id', $chat->id)
            ->with(['user', 'chat'])
            ->where('created_at', '>=', $created_at)
            ->limit(1000)
            ->get();
        $result   = '';

        foreach ($messages as $message) {
            $result .= $message->user->first_name . '(' . $message->user->username . ') [' . Carbon::createFromTimestamp($message->datetime)->toString() . ']:' . $message->text . "\n";
        }

        $telegram_users = TelegramUser::whereHas('chats', function ($query) use ($chat) {
            $query->where('telegram_chat_id', $chat->id);
        })
            ->withCount([
                'messages' => function ($query) use ($created_at) {
                    $query->where('created_at', '>=', $created_at);
                },
            ])
            ->get();

        $users = '';

        foreach ($telegram_users as $user) {
            $users .= $user->first_name . '(' . $user->username . '): ' . $user->messages_count . "\n";
        }

        return [
            'chat'    => $chat,
            'context' => $result,
            'users'   => $users,
        ];
    }

    public function getGroupChats()
    {
        $from    = request('from', '-3 days');
        $private = request('is_private', false);
        if ($private) {
            $type = [
                'private',
            ];
        } else {
            $type = [
                'group',
                'supergroup',
            ];
        }
        $created_at = Carbon::parse($from);

        $chats = TelegramChat::whereIn('type', $type)
            ->withCount([
                'messages' => function ($query) use ($created_at) {
                    $query->where('created_at', '>=', $created_at);
                },
            ])
            ->get();

        return $chats;
    }

    public function saveMessage()
    {
        request()->validate([
            'chat_id' => 'required',
            'user_id' => 'required',
            'text'    => 'required|max:50000',
        ]);

        TelegramMessage::create([
            'telegram_chat_id' => request('chat_id'),
            'telegram_user_id' => request('user_id'),
            'text'             => request('text'),
        ]);

        return response()->json([
            'success' => true,
        ]);
    }
}
