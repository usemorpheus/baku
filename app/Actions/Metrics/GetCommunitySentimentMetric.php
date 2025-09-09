<?php

namespace App\Actions\Metrics;

use Lorisleiva\Actions\Concerns\AsAction;

class GetCommunitySentimentMetric
{
    use AsAction;

    public function handle($chat, $dimension): int
    {
        return 0;
    }
}
