<?php

namespace App\Actions\Metrics;

use Lorisleiva\Actions\Concerns\AsAction;

class GetMarketCapMetric
{
    use AsAction;

    public function handle($chat, $dimension): string
    {
        return (string)rand(1000000, 100000000);
    }
}
