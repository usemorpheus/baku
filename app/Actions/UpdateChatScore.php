<?php

namespace App\Actions;

use App\Models\Metric;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateChatScore
{
    use AsAction;

    public function handle($date = null): void
    {
        $date = $date ?: date('Y-m-d');

        $metrics = Metric::where('date', $date)
            ->get();

        foreach ($metrics as $metric) {
            $metric->update([
                'baku_score' => $metric->total_members + 2 * $metric->active_members,
            ]);
        }
    }
}
