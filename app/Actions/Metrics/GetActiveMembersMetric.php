<?php

namespace App\Actions\Metrics;

use App\Models\TelegramMessage;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class GetActiveMembersMetric
{
    use AsAction;

    public function handle($chat, $dimension): int
    {
        return TelegramMessage::where('telegram_chat_id', $chat->id)
            ->select(DB::raw('count(distinct telegram_user_id) as total'))
            ->where('created_at', '>=', now()->subDays($dimension)->startOfDay())
            ->first()->total;
    }
}
