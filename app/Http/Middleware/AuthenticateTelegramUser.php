<?php

namespace App\Http\Middleware;

use App\Models\TelegramUser;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateTelegramUser
{
    public function handle(Request $request, Closure $next)
    {
        // 从请求中获取Telegram用户信息
        $telegramUserId = $request->header('X-Telegram-User-ID') ?: session('telegram_user_id');
        
        if (!$telegramUserId) {
            // 尝试从上下文中获取用户ID
            $telegramUserId = $this->getTelegramUserIdFromRequest($request);
        }
        
        if ($telegramUserId) {
            // 查找或创建Telegram用户
            $telegramUser = TelegramUser::firstOrCreate(
                ['id' => $telegramUserId],
                [
                    'first_name' => 'Unknown',
                    'username' => 'unknown_' . $telegramUserId,
                ]
            );
            
            // 将用户信息存储到session中
            session(['telegram_user_id' => $telegramUserId]);
            $request->attributes->set('telegram_user', $telegramUser);
        }
        
        return $next($request);
    }
    
    private function getTelegramUserIdFromRequest(Request $request)
    {
        // 尝试从多种来源获取Telegram用户ID
        // 1. 从请求参数
        if ($request->has('telegram_user_id')) {
            return $request->input('telegram_user_id');
        }
        
        // 2. 从会话
        if (session()->has('telegram_user_id')) {
            return session('telegram_user_id');
        }
        
        // 3. 从cookie（如果有的话）
        return null;
    }
}