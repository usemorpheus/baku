<?php

namespace App\Actions\Metrics;

use App\Actions\Telegram\GetChatMemberCount;
use Lorisleiva\Actions\Concerns\AsAction;

class GetTotalMembersMetric
{
    use AsAction;

    public function handle($chat, $dimension = null): int
    {
        $chat_id = $chat->id;
        return cache()->remember('metric_chat_total_members_' . $chat_id, 60, function () use ($chat_id) {
            return GetChatMemberCount::run($chat_id, config('baku.telegram_bot_token'));
        });
    }
}
