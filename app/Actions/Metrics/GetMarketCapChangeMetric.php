<?php

namespace App\Actions\Metrics;

use Lorisleiva\Actions\Concerns\AsAction;

class GetMarketCapChangeMetric
{
    use AsAction;

    public function handle($chat, $dimension)
    {
        return rand(-100, 100) / 100;
    }
}
