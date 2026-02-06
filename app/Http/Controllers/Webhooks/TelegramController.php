<?php

namespace App\Http\Controllers\Webhooks;

use App\Models\TelegramChat;
use App\Models\TelegramMessage;
use App\Models\TelegramUser;
use App\Models\Metric;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\UserTask;
use App\Models\TaskType;

class TelegramController
{
    public function __invoke()
    {
        $data = request()->all();

        Log::debug($data);

        $message = $data['message'] ?? $data['edited_message'] ?? $data['channel_post'] ?? $data['edited_channel_post'] ?? null;

        $chat = null;
        $user = null;
        $result = [];

        if (!empty($message)) {
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

            if (!empty($message['chat'])) {
                $chat = TelegramChat::updateOrCreate([
                    'id' => $message['chat']['id'],
                ], [
                    'type'  => $message['chat']['type'] ?? 'private',
                    'title' => $message['chat']['title'] ?? $message['chat']['username'],
                ]);
            }

            if (!empty($chat) && !empty($user)) {
                $chat->users()->syncWithoutDetaching([
                    $user->id,
                ]);
            }

            if (!empty($message['text'])) {
                TelegramMessage::updateOrCreate([
                    'id' => $message['message_id'],
                ], [
                    'telegram_user_id' => $user->id,
                    'telegram_chat_id' => $chat->id,
                    'text'             => $message['text'],
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
                
                // 保存Telegram用户ID到会话中
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
            
            // 检查是否是机器人从群组离开（通过my_chat_member事件）
            $oldChatMember = $message['old_chat_member']['user'] ?? null;
            $newChatMember = $message['new_chat_member']['user'] ?? null;
            
            if (!empty($oldChatMember) && 
                ($oldChatMember['is_bot'] ?? false) && 
                strpos($oldChatMember['username'], 'baku_news_bot') !== false &&
                ($message['old_chat_member']['status'] ?? '') === 'member' &&
                ($message['new_chat_member']['status'] ?? '') === 'left') {
                
                // 机器人从群组离开，邀请人是message['from']['id']
                $remover_user_id = $message['from']['id'] ?? null;
                if ($remover_user_id) {
                    // 撤销任务记录 - 机器人从群组被移除
                    \Log::info("Processing bot removal in main controller for chat {$chat->id}");
                    $this->revokeAddBotToGroupTask($chat->id, $remover_user_id);
                }
            }
            
            // 检查是否是机器人从群组离开（通过message事件中的left_chat_member字段）
            $leftChatMember = $message['left_chat_member']['user'] ?? null;
            if (!empty($leftChatMember) && ($leftChatMember['is_bot'] ?? false) && strpos($leftChatMember['username'], 'baku_news_bot') !== false) {
                // 机器人从群组离开，邀请人是message['from']['id']
                $remover_user_id = $message['from']['id'] ?? null;
                if ($remover_user_id) {
                    // 撤销任务记录 - 机器人从群组被移除
                    \Log::info("Processing bot removal in main controller for chat {$chat->id}");
                    $this->revokeAddBotToGroupTask($chat->id, $remover_user_id);
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
                // UpdateChatInfo::dispatch($chat);
            }
        }

        return $data;
    }

    public function getMessagesContext()
    {
        $from    = request('from', '-3 days');
        $private = request('is_private', false);
        $chat_id = request('chat_id');
        $users   = [];

        $created_at = Carbon::parse($from);

        $messages = TelegramMessage::with('user:id,first_name,username,is_bot')
            ->where('created_at', '>', $created_at);

        if ($chat_id) {
            $messages = $messages->where('telegram_chat_id', $chat_id);
        }

        if ($private) {
            $messages = $messages->whereHas('chat', function ($query) {
                $query->whereIn('type', [
                    'private',
                ]);
            });
        } else {
            $messages = $messages->whereHas('chat', function ($query) {
                $query->whereIn('type', [
                    'group',
                    'supergroup',
                ]);
            });
        }

        $messages = $messages->orderBy('created_at', 'desc')
            ->limit(request('limit', 10))
            ->get();

        foreach ($messages as $message) {
            $user = $message->user;
            if ($user) {
                $users[$user->id] = $user;
            }
        }

        $result = [];

        foreach ($messages as $message) {
            $result[] = [
                'id'       => $message->id,
                'text'     => $message->text,
                'datetime' => $message->datetime,
                'user'     => $users[$message->telegram_user_id] ?? null,
            ];
        }

        return [
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

        $chats = TelegramChat::where('created_at', '>', $created_at)
            ->whereIn('type', $type)
            ->withCount([
                'messages',
                'users',
            ])
            ->get();

        $result = [];

        foreach ($chats as $chat) {
            $result[] = [
                'id'          => $chat->id,
                'title'       => $chat->title,
                'type'        => $chat->type,
                'messages'    => $chat->messages_count,
                'users'       => $chat->users_count,
                'invite_by'   => $chat->invite_by,
                'created_at'  => $chat->created_at,
                'last_touch'  => $chat->getMeta('last_touch'),
                'baku_index'  => $chat->getMeta('baku_index'),
                'baku_score'  => $chat->getMeta('baku_score'),
                'baku_change' => $chat->getMeta('baku_change'),
            ];
        }

        return $result;
    }

    public function saveMessage(Request $request)
    {
        $message = $request->input('message');
        $chat_id = $request->input('chat_id');
        $user_id = $request->input('user_id');

        if ($message && $chat_id && $user_id) {
            $user = TelegramUser::find($user_id);
            $chat = TelegramChat::find($chat_id);

            if ($user && $chat) {
                TelegramMessage::create([
                    'id'               => time() . rand(1000, 9999),
                    'telegram_user_id' => $user->id,
                    'telegram_chat_id' => $chat->id,
                    'text'             => $message,
                    'datetime'         => time(),
                ]);

                return response()->json(['status' => 'success']);
            }
        }

        return response()->json(['status' => 'error']);
    }

    public function addBaku(Request $request)
    {
        $chat_id = $request->input('chat_id');
        $user_id = $request->input('user_id');

        if ($chat_id && $user_id) {
            $user = TelegramUser::find($user_id);
            $chat = TelegramChat::find($chat_id);

            if ($user && $chat) {
                $chat->users()->syncWithoutDetaching([
                    $user->id,
                ]);

                return response()->json(['status' => 'success']);
            }
        }

        return response()->json(['status' => 'error']);
    }

    public function saveBakuCommunityData(Request $request)
    {
        $chat_id = $request->input('chat_id');
        $data = $request->input('data');

        if ($chat_id && $data) {
            $chat = TelegramChat::find($chat_id);
            if ($chat) {
                foreach ($data as $key => $value) {
                    $chat->setMeta($key, $value);
                }

                return response()->json(['status' => 'success']);
            }
        }

        return response()->json(['status' => 'error']);
    }

    public function updateBakuMarketData(Request $request)
    {
        $chat_id = $request->input('chat_id');
        $market_data = $request->input('market_data');

        if ($chat_id && $market_data) {
            $chat = TelegramChat::find($chat_id);
            if ($chat) {
                $chat->setMeta('market_data', $market_data);
                $chat->setMeta('last_market_update', now());

                return response()->json(['status' => 'success']);
            }
        }

        return response()->json(['status' => 'error']);
    }

    public function updateBakuIndexData(Request $request)
    {
        $chat_id = $request->input('chat_id');
        $index_data = $request->input('index_data');

        if ($chat_id && $index_data) {
            $chat = TelegramChat::find($chat_id);
            if ($chat) {
                $chat->setMeta('baku_index', $index_data['index'] ?? 0);
                $chat->setMeta('baku_score', $index_data['score'] ?? 0);
                $chat->setMeta('baku_change', $index_data['change'] ?? 0);
                $chat->setMeta('last_index_update', now());

                return response()->json(['status' => 'success']);
            }
        }

        return response()->json(['status' => 'error']);
    }

    public function getMetricData(Request $request)
    {
        $metrics = Metric::orderBy('created_at', 'desc')->take(100)->get();
        
        return response()->json($metrics);
    }

    public function calculateRankings()
    {
        $dimensions = [1, 7, 30]; // 1天, 7天, 30天
        $date = now()->format('Y-m-d');
        
        // 这里实现排名计算逻辑
        foreach ($dimensions as $dimension) {
            $cutoffDate = now()->subDays($dimension)->format('Y-m-d H:i:s');
            
            $chats = TelegramChat::where('created_at', '>', $cutoffDate)->get();
            
            $chatScores = [];
            foreach ($chats as $chat) {
                $score = $chat->getMeta('baku_score') ?? 0;
                $chatScores[] = [
                    'chat_id' => $chat->id,
                    'score' => $score,
                    'dimension' => $dimension
                ];
            }
            
            // 按分数排序并分配排名
            usort($chatScores, function($a, $b) {
                return $b['score'] <=> $a['score'];
            });
            
            // 保存排名
            $rank = 1;
            foreach ($chatScores as $chatScore) {
                $chat = TelegramChat::find($chatScore['chat_id']);
                if ($chat) {
                    $chat->setMeta("rank_{$dimension}_days", $rank);
                    $rank++;
                }
            }
        }
        
        return response()->json([
            'processed_dimensions' => $dimensions,
            'message' => "Rankings calculated for {$date}"
        ]);
    }
    
    private function handleAddBotToGroupTask($user, $chat)
    {
        // 检查用户是否已经完成了添加机器人到群组的任务
        $taskType = TaskType::where('name', 'add_bot_to_group')->first();
        if (!$taskType) {
            return;
        }

        $existingTask = UserTask::where('telegram_user_id', $user->id)
            ->where('task_type_id', $taskType->id)
            ->first();

        if (!$existingTask) {
            // 创建任务记录
            UserTask::create([
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
                'verified_at' => now(),
            ]);
        } else {
            // 如果任务已存在但状态不是完成，更新为完成状态
            if ($existingTask->task_status !== 'completed') {
                $existingTask->update([
                    'task_status' => 'completed',
                    'points' => $taskType->points_reward,
                    'task_data' => array_merge($existingTask->task_data ?? [], [
                        'chat_id' => $chat->id,
                        'chat_title' => $chat->title,
                        'added_at' => now()
                    ]),
                    'completed_at' => now(),
                    'verified_at' => now(),
                ]);
            }
        }
    }
    
    /**
     * 专门处理任务相关的Telegram更新
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleTaskUpdates()
    {
        $data = request()->all();
        
        // 处理 my_chat_member 更新 - 机器人被添加到群组或被移除
        if (!empty($data['my_chat_member'])) {
            $message = $data['my_chat_member'];
            
            // 检查是否是 baku_news_bot 的状态发生了变化
            $oldChatMember = $message['old_chat_member']['user'] ?? null;
            $newChatMember = $message['new_chat_member']['user'] ?? null;
            
            // 情况1: 机器人被添加到群组 (old_chat_member.status = 'left', new_chat_member.status = 'member')
            if (!empty($oldChatMember) && 
                ($oldChatMember['is_bot'] ?? false) && 
                ($oldChatMember['username'] ?? '') === 'baku_news_bot' &&
                ($message['old_chat_member']['status'] ?? '') === 'left' &&
                ($message['new_chat_member']['status'] ?? '') === 'member') {
                
                // 获取邀请人信息（执行添加操作的人）
                $inviterId = $message['from']['id'] ?? null;
                
                if ($inviterId) {
                    // 获取或创建Telegram用户
                    $inviter_user = TelegramUser::updateOrCreate([
                        'id' => $inviterId,
                    ], [
                        'first_name' => $message['from']['first_name'] ?? 'Unknown',
                        'username' => $message['from']['username'] ?? null,
                        'is_bot' => $message['from']['is_bot'] ?? false,
                    ]);
                    
                    // 获取群组信息
                    $chat = TelegramChat::updateOrCreate([
                        'id' => $message['chat']['id'],
                    ], [
                        'type' => $message['chat']['type'] ?? 'group',
                        'title' => $message['chat']['title'] ?? $message['chat']['username'] ?? 'Unknown Group',
                    ]);
                    
                    // 创建或更新添加机器人到群组的任务
                    $this->handleAddBotToGroupTask($inviter_user, $chat);
                }
            }
            // 情况2: 机器人从群组中被移除 (需要撤销任务积分)
            elseif (!empty($oldChatMember) && 
                ($oldChatMember['is_bot'] ?? false) && 
                ($oldChatMember['username'] ?? '') === 'baku_news_bot' &&
                ($message['old_chat_member']['status'] ?? '') === 'member' &&
                ($message['new_chat_member']['status'] ?? '') === 'left') {
                
                // 机器人被移除，查找并撤销相关的任务积分
                $removerId = $message['from']['id'] ?? null;
                
                if ($removerId) {
                    // 查找执行添加操作的用户（任务完成者）
                    // 注意：在某些情况下，移除者可能不是原始添加者，这里我们撤销所有相关的任务
                    $this->revokeAddBotToGroupTask($message['chat']['id'], $removerId);
                }
                
                \Log::info('Baku bot removed from group: ' . ($message['chat']['title'] ?? $message['chat']['id']) . ' by user: ' . $removerId);
            }
        }
        
        // 处理 message 更新 - new_chat_member 事件 (新成员加入群组，包括机器人)
        if (!empty($data['message']) && !empty($data['message']['new_chat_member'])) {
            $message = $data['message'];
            $newChatMember = $message['new_chat_member'];
            
            // 检查是否是 baku_news_bot 被添加到群组
            if (($newChatMember['is_bot'] ?? false) && 
                ($newChatMember['username'] ?? '') === 'baku_news_bot') {
                
                // 获取邀请人信息
                $inviterId = $message['from']['id'] ?? null;
                
                if ($inviterId) {
                    // 获取或创建Telegram用户
                    $inviter_user = TelegramUser::updateOrCreate([
                        'id' => $inviterId,
                    ], [
                        'first_name' => $message['from']['first_name'] ?? 'Unknown',
                        'username' => $message['from']['username'] ?? null,
                        'is_bot' => $message['from']['is_bot'] ?? false,
                    ]);
                    
                    // 获取群组信息
                    $chat = TelegramChat::updateOrCreate([
                        'id' => $message['chat']['id'],
                    ], [
                        'type' => $message['chat']['type'] ?? 'group',
                        'title' => $message['chat']['title'] ?? $message['chat']['username'] ?? 'Unknown Group',
                    ]);
                    
                    // 创建或更新添加机器人到群组的任务
                    $this->handleAddBotToGroupTask($inviter_user, $chat);
                }
            }
        }
        
        // 处理 message 更新 - new_chat_members 数组事件 (多个新成员加入群组，包括机器人)
        if (!empty($data['message']) && !empty($data['message']['new_chat_members'])) {
            $message = $data['message'];
            $newChatMembers = $message['new_chat_members'];
            
            // 检查是否有 baku_news_bot 被添加到群组
            foreach ($newChatMembers as $newChatMember) {
                if (($newChatMember['is_bot'] ?? false) && 
                    ($newChatMember['username'] ?? '') === 'baku_news_bot') {
                    
                    // 获取邀请人信息
                    $inviterId = $message['from']['id'] ?? null;
                    
                    if ($inviterId) {
                        // 获取或创建Telegram用户
                        $inviter_user = TelegramUser::updateOrCreate([
                            'id' => $inviterId,
                        ], [
                            'first_name' => $message['from']['first_name'] ?? 'Unknown',
                            'username' => $message['from']['username'] ?? null,
                            'is_bot' => $message['from']['is_bot'] ?? false,
                        ]);
                        
                        // 获取群组信息
                        $chat = TelegramChat::updateOrCreate([
                            'id' => $message['chat']['id'],
                        ], [
                            'type' => $message['chat']['type'] ?? 'group',
                            'title' => $message['chat']['title'] ?? $message['chat']['username'] ?? 'Unknown Group',
                        ]);
                        
                        // 创建或更新添加机器人到群组的任务
                        $this->handleAddBotToGroupTask($inviter_user, $chat);
                    }
                }
            }
        }
        
        // 处理 message 更新 - left_chat_member 事件 (成员离开群组，包括机器人)
        if (!empty($data['message']) && !empty($data['message']['left_chat_member'])) {
            $message = $data['message'];
            $leftChatMember = $message['left_chat_member'];
            
            // 检查是否是 baku_news_bot 离开了群组
            if (($leftChatMember['is_bot'] ?? false) && 
                ($leftChatMember['username'] ?? '') === 'baku_news_bot') {
                
                // 机器人离开了群组，撤销相关任务积分
                $leaverId = $message['from']['id'] ?? null; // 执行移除操作的用户
                
                if ($leaverId) {
                    // 查找并撤销相关任务积分
                    $this->revokeAddBotToGroupTask($message['chat']['id'], $leaverId);
                }
                
                \Log::info('Baku bot left group: ' . ($message['chat']['title'] ?? $message['chat']['id']) . ' by user: ' . $leaverId);
            }
        }
        
        // 处理 message 更新 - left_chat_participant 事件 (参与者离开群组，包括机器人)
        if (!empty($data['message']) && !empty($data['message']['left_chat_participant'])) {
            $message = $data['message'];
            $leftChatParticipant = $message['left_chat_participant'];
            
            // 检查是否是 baku_news_bot 离开了群组
            if (($leftChatParticipant['is_bot'] ?? false) && 
                ($leftChatParticipant['username'] ?? '') === 'baku_news_bot') {
                
                // 机器人离开了群组，撤销相关任务积分
                $leaverId = $message['from']['id'] ?? null; // 执行移除操作的用户
                
                if ($leaverId) {
                    // 查找并撤销相关任务积分
                    $this->revokeAddBotToGroupTask($message['chat']['id'], $leaverId);
                }
                
                \Log::info('Baku bot left group (participant): ' . ($message['chat']['title'] ?? $message['chat']['id']) . ' by user: ' . $leaverId);
            }
        }
        
        return response()->json(['status' => 'ok']);
    }
    
    /**
     * 撤销添加机器人到群组的任务积分
     * 
     * @param mixed $chatId 群组ID
     * @param mixed $removerId 执行移除操作的用户ID
     * @return void
     */
    private function revokeAddBotToGroupTask($chatId, $removerId)
    {
        \Log::info("Attempting to revoke tasks for chat {$chatId}, remover: {$removerId}");
        
        // 查找对应的添加机器人任务类型
        $taskType = TaskType::where('name', 'add_bot_to_group')->first();
        
        if (!$taskType) {
            \Log::error("Task type 'add_bot_to_group' not found");
            return;
        }
        
        // 查找与此群组相关的已完成任务
        // 由于chat_id可能以不同格式存储，我们使用更通用的查询
        $completedTasks = UserTask::where('task_type_id', $taskType->id)
            ->where('task_status', 'completed')
            ->get()
            ->filter(function ($task) use ($chatId) {
                $taskData = $task->task_data;
                if (!isset($taskData['chat_id'])) {
                    return false;
                }
                // 比较chat_id，考虑数值相等性（-5145003749 == "-5145003749"）
                return strval($taskData['chat_id']) === strval($chatId);
            });
        
        \Log::info("Found " . $completedTasks->count() . " completed tasks for chat {$chatId} to revoke");
        
        if ($completedTasks->isEmpty()) {
            \Log::info("No completed tasks found for chat {$chatId} to revoke");
            return;
        }
        
        // 撤销找到的任务
        foreach ($completedTasks as $task) {
            try {
                \Log::info("Revoking task {$task->id} for user {$task->telegram_user_id} in chat {$chatId}");
                
                // 更新任务状态为已撤销
                $task->update([
                    'task_status' => 'revoked',
                ]);
                
                // 记录详细的撤销日志
                \Log::info("Task successfully revoked for user {$task->telegram_user_id} because bot was removed from group {$chatId}", [
                    'task_id' => $task->id,
                    'user_id' => $task->telegram_user_id,
                    'task_type_id' => $task->task_type_id,
                    'points_deducted' => $task->points,
                    'revoker_id' => $removerId
                ]);
            } catch (\Exception $e) {
                // 如果更新失败，记录错误
                \Log::error("Failed to revoke task for user {$task->telegram_user_id}: " . $e->getMessage(), [
                    'task_id' => $task->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
}