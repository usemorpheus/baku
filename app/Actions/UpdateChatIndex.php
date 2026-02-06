<?php

namespace App\Actions;

use App\Models\Metric;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateChatIndex
{
    use AsAction;

    public function handle($date = null): void
    {
        $date = $date ?: date('Y-m-d');

        $dimensions = [1, 7, 30];
        foreach ($dimensions as $dimension) {
            $metrics = Metric::where('date', $date)
                ->where('dimension', $dimension)
                ->orderBy('baku_score', 'desc')
                ->get();

            $rank = 1;
            foreach ($metrics as $metric) {
                $metric->update([
                    'ranking_growth_rate' => 0,
                    'baku_index'          => $rank,  // 设置排名
                ]);
                $rank++;
            }
        }
    }
}
