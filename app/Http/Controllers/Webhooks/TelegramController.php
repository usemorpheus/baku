<?php

namespace App\Http\Controllers\Webhooks;

use App\Actions\Telegram\UpdateChatInfo;
use App\Models\TelegramChat;
use App\Models\TelegramMessage;
use App\Models\TelegramUser;
use App\Models\Metric;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class TelegramController
{
    public function __invoke()
    {
        $data = request()->all();

        Log::debug($data);

        if (!empty($data['message'])) {
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

            if (!empty($message['text'])) {
                TelegramMessage::updateOrCreate([
                    'message_id' => $message['message_id'],
                ], [
                    'telegram_chat_id' => $message['chat']['id'],
                    'telegram_user_id' => $message['from']['id'] ?? null,
                    'text'             => $message['text'],
                    'data'             => $message,
                    'datetime'         => $message['date'],
                ]);
            }
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

        if (!empty($chat)) {
            $chat->setMeta('last_touch', now());
            if (!empty($user) && empty($chat->invite_by)) {
                $chat->update([
                    'invite_by' => $user->id,
                ]);
            }

            if ($chat->wasRecentlyCreated) {
                UpdateChatInfo::dispatch($chat);
            }
        }

        return $data;
    }

    public function getMessagesContext()
    {
        $chat_id = request('chat_id');
        $dimension = request('dimension', '0');
        $chat    = TelegramChat::findOrFail($chat_id);

        switch ($dimension) {
            case '1':
                $from       = '-1 days';
                break;
            case '7':
                $from       = '-7 days';
                break;
            case '30':
                $from       = '-30 days';
                break;
            default:
                $from       = request('from', '-3 days');
                break;
        }

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

    public function addBaku()
    {
        $data = request()->all();
        $from = $data['from'];
        $chat = $data['chat'];
        $add_bot = $data['add_bot'];
        $delete_bot = $data['delete_bot'];

        return response()->json([
            'success' => true,
        ]);
    }

    public function saveBakuCommunityData()
    {
        $data = request()->all();
        Log::debug($data);

        foreach ($data as $metrics) {
            $this->saveOrUpdateCommunityData($metrics);
        }

        return response()->json([
            'success' => true,
        ]);
    }

    function saveOrUpdateCommunityData($metrics)
    {
        $result = Metric::updateOrCreate(
            [
                // 唯一标识条件
                'telegram_chat_id' => $metrics['telegram_chat_id'],
                'dimension' => $metrics['dimension'],
            ],
            [
                // 要更新/创建的字段
                'date' => $metrics['date'],
                'year' => $metrics['year'],
                'month' => $metrics['month'],
                'day' => $metrics['day'],
                'dimension' => $metrics['dimension'],
                'market_cap' => is_numeric($metrics['market_cap']) ? $metrics['market_cap'] : null,
                'change' => is_numeric($metrics['price_change']) ? $metrics['price_change'] : null,
                'group_messages' => is_numeric($metrics['community_messages']) ? $metrics['community_messages'] : null,
                'total_members' => is_numeric($metrics['total_members']) ? $metrics['total_members'] : null,
                'active_members' => is_numeric($metrics['active_members']) ? $metrics['active_members'] : null,
                'key_builders' => is_numeric($metrics['key_builders']) ? $metrics['key_builders'] : null,
                'builder_level' => is_numeric($metrics['build_level']) ? $metrics['build_level'] : null,
                'baku_interactions' => is_numeric($metrics['baku_interactions']) ? $metrics['baku_interactions'] : null,
                'community_activities' => is_numeric($metrics['community_activities']) ? $metrics['community_activities'] : null,
                'voice_communications' => is_numeric($metrics['voice_sessions']) ? $metrics['voice_sessions'] : null,
                'community_sentiment' => is_numeric($metrics['community_sentiment']) ? $metrics['community_sentiment'] : null,
                'ranking_growth_rate' => is_numeric($metrics['ranking_growth_rate']) ? $metrics['ranking_growth_rate'] : null,
                // 'baku_score' => is_numeric($metrics['baku_score']) ? $metrics['baku_score'] : null,
                // 'baku_index' => is_numeric($metrics['baku_index']) ? $metrics['baku_index'] : null,
                'meta' => $metrics['meta'],
                'contract_address' => $metrics['contract_address'],
                // 'created_at' => Carbon::now()->toIso8601String(),
                'updated_at' => Carbon::now()->toIso8601String(),
            ]
        );
    }
}
