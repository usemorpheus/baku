<?php

namespace App\Http\Controllers;

use App\Models\TaskType;
use App\Models\UserTask;
use App\Models\TelegramUser;
use App\Services\TwitterVerificationService;
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
            // 如果没有Telegram用户ID，重定向到首页或其他页面
            return redirect()->route('home')->with('error', 'Please access this page through Telegram bot');
        }
        
        // 确保用户存在
        $telegramUser = TelegramUser::firstOrCreate(
            ['id' => $telegramUserId],
            [
                'first_name' => 'Unknown User',
                'username' => 'user_' . $telegramUserId,
            ]
        );
        
        $availableTasks = TaskType::where('is_active', true)->get();
        
        // 获取用户已完成和待完成的任务
        $userTasks = UserTask::with('taskType')
            ->where('telegram_user_id', $telegramUserId)
            ->get();
        
        $completedTasks = $userTasks->filter(function ($task) {
            return $task->task_status === 'completed';
        });
        
        $pendingTasks = $userTasks->filter(function ($task) {
            return $task->task_status !== 'completed';
        });

        return view('tasks.index', compact('availableTasks', 'completedTasks', 'pendingTasks', 'telegramUser'));
    }

    public function claimTask(Request $request, $taskId)
    {
        // 获取Telegram用户ID
        $telegramUserId = $this->getTelegramUserId($request);
        
        if (!$telegramUserId) {
            return redirect()->route('home')->withErrors(['error' => 'User not authenticated']);
        }

        $taskType = TaskType::findOrFail($taskId);

        // 检查用户是否已经申请过此任务
        $existingTask = UserTask::where('telegram_user_id', $telegramUserId)
            ->where('task_type_id', $taskType->id)
            ->first();

        if ($existingTask) {
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
        // 从任务数据中获取用户的Twitter用户名
        $twitterUsername = $userTask->task_data['twitter_username'] ?? null;
        
        if (!$twitterUsername) {
            return [
                'success' => false,
                'message' => 'Please provide your Twitter username for verification',
                'details' => ['requires_twitter_username' => true]
            ];
        }

        try {
            // 使用Twitter验证服务检查关注状态
            $isFollowing = $this->twitterService->verifyFollow(
                $twitterUsername,
                'Baku_builders'  // 目标账户
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
            return redirect()->route('home')->withErrors(['error' => 'User not authenticated']);
        }

        $userTask = UserTask::where('id', $userTaskId)
            ->where('telegram_user_id', $telegramUserId)
            ->firstOrFail();
        
        // 根据任务类型更新任务数据
        $updateData = [
            'task_status' => 'pending',
            'task_data' => array_merge($userTask->task_data ?? [], $request->all())
        ];
        
        // 如果是Twitter任务，尝试验证
        if ($userTask->taskType->name === 'follow_twitter' && isset($request->twitter_username)) {
            $verificationResult = $this->verifyTwitterFollow($userTask);
            if ($verificationResult['success']) {
                $updateData['task_status'] = 'completed';
                $updateData['verified_at'] = now();
                $updateData['completed_at'] = now();
            }
        }
        
        $userTask->update($updateData);

        return redirect()->back()->with('status', $verificationResult['message'] ?? 'Verification data submitted. Please wait for review.');
    }

    public function getUserPoints(Request $request)
    {
        $telegramUserId = $this->getTelegramUserId($request);
        
        if (!$telegramUserId) {
            return response()->json(['points' => 0]);
        }

        $totalPoints = UserTask::where('telegram_user_id', $telegramUserId)
            ->where('task_status', 'completed')
            ->sum('points');

        return response()->json(['points' => $totalPoints]);
    }
    
    private function getTelegramUserId(Request $request)
    {
        // 优先级顺序获取Telegram用户ID：
        // 1. 请求参数
        // 2. Session
        // 3. Cookie
        // 4. 从Telegram webhook数据（如果有的话）
        
        $telegramUserId = $request->input('telegram_user_id');
        
        if (!$telegramUserId) {
            $telegramUserId = session('telegram_user_id');
        }
        
        if (!$telegramUserId) {
            $telegramUserId = $request->cookie('telegram_user_id');
        }
        
        // 如果以上都没有，尝试从Telegram webhook上下文获取
        if (!$telegramUserId && $request->has('_telegram_context')) {
            $telegramUserId = $request->input('_telegram_context.user_id');
        }
        
        return $telegramUserId;
    }
}