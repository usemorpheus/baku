<?php

namespace App\Actions\Metrics;

use Lorisleiva\Actions\Concerns\AsAction;

class GetBuilderLevelMetric
{
    use AsAction;

    public function handle($chat, $dimension): int
    {
        return 1;
    }
}
