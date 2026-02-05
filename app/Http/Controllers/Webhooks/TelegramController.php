<?php

namespace App\Http\Controllers\Webhooks;

use App\Actions\Telegram\UpdateChatInfo;
use App\Models\TelegramChat;
use App\Models\TelegramMessage;
use App\Models\TelegramUser;
use App\Models\Metric;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
                
                // 保存Telegram用户ID到会话中，以便Web端可以识别用户
                session(['telegram_user_id' => $user->id]);
                
                // 标记用户为已激活状态
                $user->update([
                    'is_activated' => true,
                    'activated_at' => now()
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
                
                // 保存Telegram用户ID到会话中（主要用于同一次请求）
                session(['telegram_user_id' => $user->id]);
                
                // 标记用户为已激活状态
                $user->update([
                    'is_activated' => true,
                    'activated_at' => now()
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
            
            // 检查是否是机器人被添加到群组
            // 当机器人被添加到群组时，new_chat_member是机器人，from是邀请人
            $newChatMember = $message['new_chat_member']['user'] ?? null;
            if (!empty($newChatMember) && ($newChatMember['is_bot'] ?? false) && strpos($newChatMember['username'], 'baku_news_bot') !== false) {
                // 机器人被添加到群组，邀请人是message['from']['id']
                $inviter_user = TelegramUser::where('id', $message['from']['id'])->first();
                if ($inviter_user) {
                    // 创建任务记录 - 添加机器人到群组任务
                    $this->handleAddBotToGroupTask($inviter_user, $chat);
                }
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

        // 如果提供了日期参数，执行排名计算
        $date = request('date', null);
        if ($date) {
            app(\App\Actions\CalculateRankings::class)->handle($date);
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
                // 'market_cap' => is_numeric($metrics['market_cap']) ? $metrics['market_cap'] : null,
                // 'change' => is_numeric($metrics['price_change']) ? $metrics['price_change'] : null,
                'group_messages' => is_numeric($metrics['community_messages']) ? $metrics['community_messages'] : null,
                'total_members' => is_numeric($metrics['total_members']) ? $metrics['total_members'] : null,
                'active_members' => is_numeric($metrics['active_members']) ? $metrics['active_members'] : null,
                'key_builders' => is_numeric($metrics['key_builders']) ? $metrics['key_builders'] : null,
                'builder_level' => is_numeric($metrics['build_level']) ? $metrics['build_level'] : null,
                'baku_interactions' => is_numeric($metrics['baku_interactions']) ? $metrics['baku_interactions'] : null,
                'community_activities' => is_numeric($metrics['community_activities']) ? $metrics['community_activities'] : null,
                'voice_communications' => is_numeric($metrics['voice_sessions']) ? $metrics['voice_sessions'] : null,
                'community_sentiment' => is_numeric($metrics['community_sentiment']) ? $metrics['community_sentiment'] : null,
                // 'ranking_growth_rate' => is_numeric($metrics['ranking_growth_rate']) ? $metrics['ranking_growth_rate'] : null,
                // 'baku_score' => is_numeric($metrics['baku_score']) ? $metrics['baku_score'] : null,
                // 'baku_index' => is_numeric($metrics['baku_index']) ? $metrics['baku_index'] : null,
                'meta' => $metrics['meta'],
                'contract_address' => $metrics['contract_address'],
                // 'created_at' => Carbon::now()->toIso8601String(),
                'updated_at' => Carbon::now()->toIso8601String(),
            ]
        );
    }

    public function getMetricData()
    {
        $metricsArray = Metric::select([
            'id',
            'contract_address',
            'last_price',
            'price',
            'last_baku_index',
            'baku_index',
            'group_messages',
            'active_members',
            'key_builders',
            'baku_interactions',
            'community_activities',
            'voice_communications',
            'community_sentiment',
            'ranking_growth_rate',
            'builder_level',
            'telegram_chat_id',
            'dimension',
        ])->get()->toArray();

        return $metricsArray;
    }

    public function updateBakuMarketData()
    {
        $data = request()->all();
        Log::debug($data);

        foreach ($data as $metrics) {
            $result = Metric::updateOrCreate(
                [
                    // 唯一标识条件
                    'telegram_chat_id' => $metrics['telegram_chat_id'],
                    'dimension' => $metrics['dimension'],
                ],
                [
                    'market_cap' => $metrics['market_cap'],
                    'change' => $metrics['change'],
                    'price' => $metrics['price'],
                    'last_price' => $metrics['last_price'],
                    'updated_at' => Carbon::now()->toIso8601String(),
                ]
            );
        }

        return response()->json([
            'success' => true,
        ]);
    }

    public function updateBakuIndexData()
    {
        $data = request()->all();
        Log::debug($data);

        foreach ($data as $metrics) {
            $result = Metric::updateOrCreate(
                [
                    // 唯一标识条件
                    'telegram_chat_id' => $metrics['telegram_chat_id'],
                    'dimension' => $metrics['dimension'],
                ],
                [
                    'ranking_growth_rate' => $metrics['ranking_growth_rate'],
                    'baku_score' => intval($metrics['baku_score']),
                    'baku_index' => intval($metrics['baku_index']),
                    'last_baku_index' => intval($metrics['last_baku_index']),
                    'updated_at' => Carbon::now()->toIso8601String(),
                ]
            );
        }

        return response()->json([
            'success' => true,
        ]);
    }

    public function calculateRankings()
    {
        $date = request('date', null);
        if (!$date) {
            $date = date('Y-m-d');
        }

        // 按维度分别计算排名
        $dimensions = [1, 7, 30];

        foreach ($dimensions as $dimension) {
            // 获取指定日期和维度的所有指标，按某个关键指标降序排列
            // 这里使用 baku_score 作为排序依据，你可以根据需要调整
            $metrics = Metric::where('dimension', $dimension)
                ->orderBy('baku_score', 'desc')
                ->get();

            // 为每个指标分配排名
            $rank = 1;
            foreach ($metrics as $metric) {
                $metric->update([
                    'baku_index' => $rank           // baku_index 也设置为排名
                ]);
                $rank++;
            }
        }

        return response()->json([
            'success' => true,
            'date' => $date,
            'processed_dimensions' => $dimensions,
            'message' => "Rankings calculated for {$date}"
        ]);
    }
    
    private function handleAddBotToGroupTask($user, $chat)
    {
        // 检查用户是否已经完成了添加机器人到群组的任务
        $taskType = \App\Models\TaskType::where('name', 'add_bot_to_group')->first();
        if (!$taskType) {
            return;
        }

        $existingTask = \App\Models\UserTask::where('telegram_user_id', $user->id)
            ->where('task_type_id', $taskType->id)
            ->first();

        if (!$existingTask) {
            // 创建任务记录
            \App\Models\UserTask::create([
                'telegram_user_id' => $user->id,
                'task_type_id' => $taskType->id,
                'task_status' => 'completed', // 机器人被添加到群组即认为任务完成
                'points' => $taskType->points_reward,
                'task_data' => [
                    'chat_id' => $chat->id,
                    'chat_title' => $chat->title,
                    'added_at' => now()
                ],
                'completed_at' => now(),
                'verified_at' => now()
            ]);
        }
    }
}
    
