<?php

namespace App\Actions;

use App\Models\Metric;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class CalculateRankings
{
    use AsAction;

    public function handle($date = null): void
    {
        $date = $date ?: date('Y-m-d');

        // 按维度分别计算排名
        $dimensions = [1, 7, 30];

        foreach ($dimensions as $dimension) {
            // 获取指定维度的所有指标，按 baku_score 降序排列
            $metrics = Metric::where('dimension', $dimension)
                ->orderBy('baku_score', 'desc')
                ->get();

            // 为每个指标分配排名（从1开始递增）
            $rank = 1;
            foreach ($metrics as $metric) {
                $metric->update([
                    'baku_index' => $rank           // baku_index 设置为排名
                ]);
                $rank++;
            }
        }
    }
}
