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
            // 获取指定日期和维度的所有指标，按 baku_score 降序排列
            $metrics = Metric::where('date', $date)
                ->where('dimension', $dimension)
                ->orderBy('baku_score', 'desc')
                ->get();

            // 为每个指标分配排名
            $rank = 1;
            foreach ($metrics as $metric) {
                $metric->update([
                    'ranking_growth_rate' => $rank,  // 使用 ranking_growth_rate 存储实际排名
                    'baku_index' => $rank           // baku_index 也设置为排名
                ]);
                $rank++;
            }
        }
    }
}