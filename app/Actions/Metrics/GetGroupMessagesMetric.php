<?php

namespace App\Actions\Metrics;

use Lorisleiva\Actions\Concerns\AsAction;

class GetGroupMessagesMetric
{
    use AsAction;

    public function handle($chat, $dimension)
    {
        return $chat->messages()->where('created_at', '>=', now()->subDays($dimension)->startOfDay())->count();
    }
}
