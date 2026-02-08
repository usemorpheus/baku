<?php

namespace App\Http\Controllers;

use App\Models\TaskType;
use App\Models\UserTask;
use App\Models\TelegramUser;
use App\Services\TwitterVerificationService;
use App\Actions\Telegram\GetChatMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class TaskController extends Controller
{
    private $twitterService;

    public function __construct(TwitterVerificationService $twitterService)
    {
        $this->twitterService = $twitterService;
    }

    public function index(Request $request)
    {
        // 获取Telegram用户ID
        $telegramUserId = $this->getTelegramUserId($request);
        
        if (!$telegramUserId) {
            // 如果没有Telegram用户ID，检查是否有最近激活的用户
            try {
                $recentActivatedUser = \App\Models\TelegramUser::where('is_activated', true)
                    ->where('activated_at', '>', now()->subMinutes(30)) // 最近30分钟内激活的用户
                    ->latest('activated_at')
                    ->first();
                    
                if ($recentActivatedUser) {
                    // 如果有最近激活的用户，将该用户ID存储在会话中并继续
                    session(['telegram_user_id' => $recentActivatedUser->id]);
                    $telegramUserId = $recentActivatedUser->id;
                } else {
                    // 如果没有最近激活的用户，显示连接说明页面
                    return view('connect-telegram');
                }
            } catch (\Exception $e) {
                // 如果字段不存在，跳过激活检查，直接显示连接说明页面
                return view('connect-telegram');
            }
        }
        
        // 确保用户存在
        $telegramUser = TelegramUser::firstOrCreate(
            ['id' => $telegramUserId],
            [
                'first_name' => 'Unknown User',
                'username' => 'user_' . $telegramUserId,
            ]
        );
        
        // 获取用户已完成和待完成的任务
        $userTasks = UserTask::with('taskType')
            ->where('telegram_user_id', $telegramUserId)
            ->get();
        
        // 获取用户已认领但未撤销的任务作为不可用任务
        $claimedAndActiveTaskTypeIds = $userTasks->filter(function ($task) {
            return $task->task_status !== 'revoked'; // 只有非revoked的任务才被视为已认领
        })->pluck('task_type_id')->toArray();
        
        $availableTasks = TaskType::where('is_active', true)
            ->whereNotIn('id', $claimedAndActiveTaskTypeIds)
            ->get();
        
        // 按状态分组任务
        $pendingTasks = $userTasks->filter(function ($task) {
            return $task->task_status === 'pending';
        });
        
        $completedTasks = $userTasks->filter(function ($task) {
            return $task->task_status === 'completed';
        });
        
        $revokedTasks = $userTasks->filter(function ($task) {
            return $task->task_status === 'revoked';
        });

        return view('tasks.index', compact('availableTasks', 'completedTasks', 'pendingTasks', 'revokedTasks', 'telegramUser'));
    }

    public function claimTask(Request $request, $taskId)
    {
        // 获取Telegram用户ID
        $telegramUserId = $this->getTelegramUserId($request);
        
        if (!$telegramUserId) {
            // 检查是否有最近激活的用户
            try {
                $recentActivatedUser = \App\Models\TelegramUser::where('is_activated', true)
                    ->where('activated_at', '>', now()->subMinutes(30)) // 最近30分钟内激活的用户
                    ->latest('activated_at')
                    ->first();
                    
                if ($recentActivatedUser) {
                    // 如果有最近激活的用户，将该用户ID存储在会话中
                    session(['telegram_user_id' => $recentActivatedUser->id]);
                    $telegramUserId = $recentActivatedUser->id;
                } else {
                    return redirect()->route('home')->withErrors(['error' => 'Please connect with Telegram bot first to claim tasks. Start a conversation with @baku_news_bot.']);
                }
            } catch (\Exception $e) {
                // 如果字段不存在，跳过激活检查，直接重定向
                return redirect()->route('home')->withErrors(['error' => 'Please connect with Telegram bot first to claim tasks. Start a conversation with @baku_news_bot.']);
            }
        }

        $taskType = TaskType::findOrFail($taskId);

        // 检查用户是否已经申请过此任务（但允许重新申请已撤销的任务）
        $existingTask = UserTask::where('telegram_user_id', $telegramUserId)
            ->where('task_type_id', $taskType->id)
            ->first();

        if ($existingTask && $existingTask->task_status !== 'revoked') {
            return redirect()->back()->withErrors(['error' => 'Task already claimed']);
        }

        // 创建用户任务记录
        $userTask = UserTask::create([
            'telegram_user_id' => $telegramUserId,
            'task_type_id' => $taskType->id,
            'task_status' => 'pending', // 默认为待验证
            'points' => $taskType->points_reward,
            'task_data' => []
        ]);

        // 根据任务类型执行不同的验证逻辑
        $verificationResult = $this->verifyTask($userTask);

        if ($verificationResult['success']) {
            $userTask->update([
                'task_status' => 'completed',
                'verified_at' => now(),
                'completed_at' => now()
            ]);
        } else {
            $userTask->update([
                'task_status' => 'pending',
                'task_data' => array_merge($userTask->task_data ?? [], $verificationResult['details'])
            ]);
        }

        return redirect()->route('tasks.index')->with('status', $verificationResult['message']);
    }

    private function verifyTask(UserTask $userTask)
    {
        $taskType = $userTask->taskType;
        
        switch ($taskType->name) {
            case 'add_bot_to_group':
                // 机器人被添加到群组的任务应该已经在Telegram webhook中处理
                // 这里检查是否已经完成
                if ($userTask->task_data && isset($userTask->task_data['added_at'])) {
                    return [
                        'success' => true,
                        'message' => 'Bot added to group task completed',
                        'details' => []
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'Bot added to group task requires verification',
                        'details' => ['requires_manual_verification' => true]
                    ];
                }
                
            case 'follow_twitter':
                // 验证Twitter关注
                return $this->verifyTwitterFollow($userTask);
                
            case 'join_telegram_channel':
                return $this->verifyJoinTelegramChannel($userTask);

            case 'retweet_post':
                // 认领时无 tweet_url，保持待验证
                return [
                    'success' => false,
                    'message' => 'Please submit your retweet link and Twitter username for verification',
                    'details' => ['requires_tweet_url_and_username' => true]
                ];
                
            default:
                return [
                    'success' => false,
                    'message' => 'Unknown task type',
                    'details' => ['error' => 'Unknown task type']
                ];
        }
    }

    private function verifyTwitterFollow(UserTask $userTask)
    {
        $taskData = is_array($userTask->task_data) ? $userTask->task_data : (array) $userTask->task_data;
        $twitterUsername = $taskData['twitter_username'] ?? null;

        if (!$twitterUsername || !is_string($twitterUsername) || trim($twitterUsername) === '') {
            return [
                'success' => false,
                'message' => 'Please provide your Twitter username for verification',
                'details' => ['requires_twitter_username' => true]
            ];
        }

        $twitterUsername = trim($twitterUsername);
        $targetUsername = config('services.twitter.follow_target', 'Baku_builders');

        try {
            $isFollowing = $this->twitterService->verifyFollow(
                $twitterUsername,
                $targetUsername
            );

            if ($isFollowing) {
                return [
                    'success' => true,
                    'message' => 'Twitter follow task verified successfully',
                    'details' => []
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Twitter follow task requires manual verification',
                    'details' => ['requires_manual_verification' => true]
                ];
            }
        } catch (\Exception $e) {
            \Log::error('Twitter verification error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Twitter verification temporarily unavailable, please try again later',
                'details' => ['verification_error' => true]
            ];
        }
    }

    public function submitVerification(Request $request, $userTaskId)
    {
        // 获取Telegram用户ID
        $telegramUserId = $this->getTelegramUserId($request);
        
        if (!$telegramUserId) {
            // 检查是否有最近激活的用户
            try {
                $recentActivatedUser = \App\Models\TelegramUser::where('is_activated', true)
                    ->where('activated_at', '>', now()->subMinutes(30)) // 最近30分钟内激活的用户
                    ->latest('activated_at')
                    ->first();
                    
                if ($recentActivatedUser) {
                    // 如果有最近激活的用户，将该用户ID存储在会话中
                    session(['telegram_user_id' => $recentActivatedUser->id]);
                    $telegramUserId = $recentActivatedUser->id;
                } else {
                    return redirect()->route('home')->withErrors(['error' => 'Please connect with Telegram bot first to submit verification. Start a conversation with @baku_news_bot.']);
                }
            } catch (\Exception $e) {
                // 如果字段不存在，跳过激活检查，直接重定向
                return redirect()->route('home')->withErrors(['error' => 'Please connect with Telegram bot first to submit verification. Start a conversation with @baku_news_bot.']);
            }
        }

        $userTask = UserTask::where('id', $userTaskId)
            ->where('telegram_user_id', $telegramUserId)
            ->firstOrFail();

        $mergedTaskData = array_merge($userTask->task_data ?? [], $request->all());
        // 提交验证时清除之前的不通过原因，便于重新审核
        unset($mergedTaskData['rejection_reason']);

        $updateData = [
            'task_status' => 'pending',
            'task_data' => $mergedTaskData,
        ];

        $verificationResult = ['message' => 'Verification data submitted. Please wait for review.'];

        // 关注 Twitter：先 API 验证，未通过则标记为待人工审核
        if ($userTask->taskType->name === 'follow_twitter' && isset($request->twitter_username)) {
            $userTask->task_data = $mergedTaskData;
            $verificationResult = $this->verifyTwitterFollow($userTask);
            if ($verificationResult['success']) {
                $updateData['task_status'] = 'completed';
                $updateData['verified_at'] = now();
                $updateData['completed_at'] = now();
            } else {
                $mergedTaskData['under_review'] = true;
                $mergedTaskData['submitted_at'] = now()->toIso8601String();
                $updateData['task_data'] = $mergedTaskData;
            }
        }

        // 转发推文：先 API 验证，未通过则标记为待人工审核
        if ($userTask->taskType->name === 'retweet_post' && isset($request->tweet_url)) {
            $userTask->task_data = $mergedTaskData;
            $retweetVerified = $this->verifyRetweet($userTask);
            if ($retweetVerified) {
                $verificationResult = ['message' => 'Retweet task verified successfully'];
                $updateData['task_status'] = 'completed';
                $updateData['verified_at'] = now();
                $updateData['completed_at'] = now();
            } else {
                $verificationResult = ['message' => 'Submitted. Under review.'];
                $mergedTaskData['under_review'] = true;
                $mergedTaskData['submitted_at'] = now()->toIso8601String();
                $updateData['task_data'] = $mergedTaskData;
            }
        }

        // 加入 Telegram 频道：仅 API 验证
        if ($userTask->taskType->name === 'join_telegram_channel') {
            $channelVerified = $this->verifyJoinTelegramChannelForUser($telegramUserId);
            $verificationResult = $channelVerified
                ? ['message' => 'Channel join verified successfully']
                : ['message' => 'Please join the channel @' . config('baku.telegram_channel_username', 'bakubuilders') . ' and try again'];
            if ($channelVerified) {
                $updateData['task_status'] = 'completed';
                $updateData['verified_at'] = now();
                $updateData['completed_at'] = now();
            }
        }

        $userTask->update($updateData);

        return redirect()->back()->with('status', $verificationResult['message']);
    }

    public function getUserPoints(Request $request)
    {
        $telegramUserId = $this->getTelegramUserId($request);
        
        if (!$telegramUserId) {
            // 检查是否有最近激活的用户
            try {
                $recentActivatedUser = \App\Models\TelegramUser::where('is_activated', true)
                    ->where('activated_at', '>', now()->subMinutes(30)) // 最近30分钟内激活的用户
                    ->latest('activated_at')
                    ->first();
                    
                if ($recentActivatedUser) {
                    // 如果有最近激活的用户，将该用户ID存储在会话中
                    session(['telegram_user_id' => $recentActivatedUser->id]);
                    $telegramUserId = $recentActivatedUser->id;
                } else {
                    return response()->json(['points' => 0]);
                }
            } catch (\Exception $e) {
                // 如果字段不存在，跳过激活检查，返回0积分
                return response()->json(['points' => 0]);
            }
        }

        // 计算用户的有效积分（完成的加，撤销的减）
        $totalPoints = UserTask::where('telegram_user_id', $telegramUserId)
            ->whereIn('task_status', ['completed', 'revoked'])
            ->get()
            ->sum(function ($task) {
                return $task->effective_points;
            });

        // 确保积分不会为负数
        $totalPoints = max(0, $totalPoints);

        return response()->json(['points' => $totalPoints]);
    }
    
    /**
     * 验证用户是否已加入指定 Telegram 频道（用于认领时或提交验证时）
     */
    private function verifyJoinTelegramChannel(UserTask $userTask): array
    {
        $telegramUserId = $userTask->telegram_user_id;
        $isMember = $this->verifyJoinTelegramChannelForUser($telegramUserId);
        if ($isMember) {
            return [
                'success' => true,
                'message' => 'Channel join verified successfully',
                'details' => []
            ];
        }
        return [
            'success' => false,
            'message' => 'Please join the official Telegram channel and try again',
            'details' => ['requires_manual_verification' => true]
        ];
    }

    /**
     * 根据 Telegram 用户 ID 检查是否在配置的官方频道内
     */
    private function verifyJoinTelegramChannelForUser(string $telegramUserId): bool
    {
        $channel = config('baku.telegram_channel_username', 'bakubuilders');
        $chatId = str_starts_with($channel, '@') ? $channel : '@' . $channel;
        return GetChatMember::isMember($chatId, $telegramUserId);
    }

    private function verifyRetweet(UserTask $userTask)
    {
        $taskData = is_array($userTask->task_data) ? $userTask->task_data : (array) $userTask->task_data;
        $tweetUrl = $taskData['tweet_url'] ?? null;
        $twitterUsername = $taskData['twitter_username'] ?? null;

        if (!$tweetUrl || !$twitterUsername || trim((string) $twitterUsername) === '') {
            return [
                'success' => false,
                'message' => 'Please provide tweet URL and your Twitter username for verification',
                'details' => ['requires_tweet_url_and_username' => true]
            ];
        }

        $requiredPinnedFrom = config('services.twitter.retweet_pinned_from', 'Baku_builders');

        try {
            $isRetweeted = $this->twitterService->verifyRetweet(
                trim((string) $tweetUrl),
                trim((string) $twitterUsername),
                $requiredPinnedFrom
            );

            if ($isRetweeted) {
                return [
                    'success' => true,
                    'message' => 'Retweet task verified successfully',
                    'details' => []
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Retweet task requires manual verification',
                    'details' => ['requires_manual_verification' => true]
                ];
            }
        } catch (\Exception $e) {
            \Log::error('Twitter retweet verification error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Twitter verification temporarily unavailable, please try again later',
                'details' => ['verification_error' => true]
            ];
        }
    }
    
    public function verifyAuth(Request $request)
    {
        $telegramUserId = $this->getTelegramUserId($request);
        
        // 尝试从会话中获取用户ID
        if (!$telegramUserId) {
            $telegramUserId = session('telegram_user_id');
        }
        
        // 如果仍然没有用户ID，检查是否有最近激活的用户
        if (!$telegramUserId) {
            try {
                $recentActivatedUser = \App\Models\TelegramUser::where('is_activated', true)
                    ->where('activated_at', '>', now()->subMinutes(30)) // 最近30分钟内激活的用户
                    ->latest('activated_at')
                    ->first();
                    
                if ($recentActivatedUser) {
                    // 如果有最近激活的用户，将该用户ID存储在会话中
                    session(['telegram_user_id' => $recentActivatedUser->id]);
                    $telegramUserId = $recentActivatedUser->id;
                }
            } catch (\Exception $e) {
                // 如果字段不存在，跳过激活检查
                $telegramUserId = null;
            }
        }
        
        return response()->json([
            'authenticated' => !empty($telegramUserId),
            'message' => $telegramUserId ? 'User authenticated' : 'User not authenticated',
            'telegram_user_id' => $telegramUserId
        ]);
    }
    
    private function getTelegramUserId(Request $request)
    {
        // 优先级顺序获取Telegram用户ID：
        // 1. 请求参数
        // 2. Session
        // 3. Cookie
        // 4. 从数据库认证令牌
        // 5. 从Telegram webhook数据（如果有的话）
        
        $telegramUserId = $request->input('telegram_user_id');
        
        if (!$telegramUserId) {
            $telegramUserId = session('telegram_user_id');
        }
        
        if (!$telegramUserId) {
            $telegramUserId = $request->cookie('telegram_user_id');
        }
        
        // 从数据库认证令牌获取（如果会话丢失但仍需识别用户）
        if (!$telegramUserId) {
            $telegramUserId = $this->getTelegramUserIdFromAuthToken();
        }
        
        // 如果以上都没有，尝试从Telegram webhook上下文获取
        if (!$telegramUserId && $request->has('_telegram_context')) {
            $telegramUserId = $request->input('_telegram_context.user_id');
        }
        
        return $telegramUserId;
    }
    
    /**
     * 从数据库认证令牌获取Telegram用户ID
     */
    /**
     * 从数据库认证令牌获取Telegram用户ID
     */
    private function getTelegramUserIdFromAuthToken()
    {
        // 此方法已弃用，因为我们不再使用认证令牌
        return null;
    }
    
    /**
     * 检查用户是否曾经激活过（通过is_activated字段）
     */
    private function getTelegramUserIdFromActivation()
    {
        // 如果用户已经激活过，但在当前会话中没有ID，我们无法确定具体是哪个用户
        // 所以这个方法需要结合其他方式使用
        return null; // 目前暂时返回null，因为我们无法在不知道用户ID的情况下确定是哪个用户
    }
    
    public function userProfile($telegramUserId)
    {
        $telegramUser = TelegramUser::find($telegramUserId);
        
        if (!$telegramUser) {
            abort(404, 'User not found');
        }
        
        // 获取用户完成的任务统计
        $completedTasksCount = UserTask::where('telegram_user_id', $telegramUserId)
            ->where('task_status', 'completed')
            ->count();
            
        $revokedTasksCount = UserTask::where('telegram_user_id', $telegramUserId)
            ->where('task_status', 'revoked')
            ->count();
            
        $totalPoints = UserTask::where('telegram_user_id', $telegramUserId)
            ->whereIn('task_status', ['completed', 'revoked'])
            ->get()
            ->sum(function ($task) {
                return $task->effective_points;
            });
            
        $totalPoints = max(0, $totalPoints); // 确保积分不为负数
        
        return view('user.profile', compact('telegramUser', 'completedTasksCount', 'revokedTasksCount', 'totalPoints'));
    }
}