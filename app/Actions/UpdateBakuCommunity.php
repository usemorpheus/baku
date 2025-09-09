<?php

namespace App\Actions;

use Lorisleiva\Actions\Concerns\AsAction;

class UpdateBakuCommunity
{
    use AsAction;

    public $commandSignature = 'baku:community_metrics';

    public function handle(): void
    {
        UpdateChatMetrics::run();
        UpdateChatIndex::run();
    }
}
