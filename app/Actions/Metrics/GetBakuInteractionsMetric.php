<?php

namespace App\Actions\Metrics;

use Lorisleiva\Actions\Concerns\AsAction;

class GetBakuInteractionsMetric
{
    use AsAction;

    public function handle($chat, $dimension): int
    {
        return rand(0, 500);
    }
}
