<?php

namespace App\Actions\Telegram;

use Illuminate\Support\Facades\Http;
use Lorisleiva\Actions\Concerns\AsAction;

class GetChatMember
{
    use AsAction;

    /**
     * 获取指定聊天中成员信息（用于验证用户是否在频道/群组中）
     * 机器人需为该频道管理员才能查询成员。
     *
     * @param string|int $chatId 频道用户名（如 @bakubuilders）或 chat id
     * @param string|int $userId Telegram 用户 ID
     * @param string|null $token 机器人 token，默认从 config 读取
     * @return array|null 成功返回 result（含 status 等），失败返回 null
     */
    public function handle($chatId, $userId, $token = null)
    {
        $token = $token ?: config('baku.telegram_bot_token');
        if (empty($token)) {
            return null;
        }

        $response = Http::get("https://api.telegram.org/bot{$token}/getChatMember", [
            'chat_id' => $chatId,
            'user_id' => $userId,
        ])->json();

        if ($response['ok'] ?? false) {
            return $response['result'] ?? null;
        }

        return null;
    }

    /**
     * 判断用户是否为频道成员（非 left/kicked）
     */
    public static function isMember($chatId, $userId, $token = null): bool
    {
        $result = static::run($chatId, $userId, $token);
        if (!$result || !isset($result['status'])) {
            return false;
        }
        $status = strtolower((string) $result['status']);
        return !in_array($status, ['left', 'kicked'], true);
    }
}
