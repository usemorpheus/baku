<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Metric;
use App\Models\UserTask;
use App\Models\TelegramUser;
use Cache;
use Carbon\Carbon;

class ActivityController
{
    public function community()
    {
        $data      = [
            'tab' => 'community',
        ];
        $dimension = request('dimension', '1');

        // 验证维度参数的有效性
        $validDimensions = ['1', '7', '30'];
        if (!in_array($dimension, $validDimensions)) {
            $dimension = '1'; // 默认值
        }

        try {
            // 获取最新日期的数据
            $latestDate = Metric::where('dimension', $dimension)
                ->max('date');
            
            if (!$latestDate) {
                $latestDate = now()->format('Y-m-d');
            }

            $data['metrics'] = Metric::with(['chat' => function($query) {
                    $query->select('id', 'title', 'type', 'data'); // 仅选择必要的字段
                }])
                ->where('dimension', $dimension)
                ->where('date', $latestDate)
                ->orderBy('baku_index')
                ->paginate(15) // 明确指定每页数量
                ->appends(request()->except('page'));

            $data['current_date'] = $latestDate;
        } catch (\Exception $e) {
            // 如果查询失败，记录错误并返回空集合
            \Log::error('Community metrics query failed: ' . $e->getMessage());
            $data['metrics'] = collect([]);
            $data['current_date'] = now()->format('Y-m-d');
        }

        return $this->response('community', $data);
    }

    public function points()
    {
        $data = [
            'tab' => 'points',
        ];

        // 获取积分最高的用户排行榜
        // 按用户ID分组，计算每个用户的净积分（完成的积分减去撤销的积分），确保不为负数
        $userPoints = UserTask::selectRaw('
            telegram_user_id,
            GREATEST(0, 
                SUM(
                    CASE 
                        WHEN task_status = \'completed\' THEN points
                        WHEN task_status = \'revoked\' THEN -points
                        ELSE 0
                    END
                )
            ) AS total_points
        ')
        ->whereIn('task_status', ['completed', 'revoked'])
        ->groupBy('telegram_user_id')
        ->orderBy('total_points', 'desc')
        ->paginate(15)
        ->appends(request()->except('page'));

        // 获取用户详情
        $userIds = $userPoints->pluck('telegram_user_id')->toArray();
        $users = TelegramUser::whereIn('id', $userIds)->get()->keyBy('id');

        // 将用户信息合并到结果中
        $rankedUsers = $userPoints->map(function ($userPoint) use ($users) {
            $user = $users->get($userPoint->telegram_user_id) ?? null;
            return [
                'user' => $user,
                'total_points' => $userPoint->total_points,
            ];
        });

        $data['ranked_users'] = $rankedUsers;
        $data['paginator'] = $userPoints;

        return $this->response('points', $data);
    }

    protected function response($view, $data)
    {

        $data['twitter_link']      = "https://x.com/Baku_builders";
        $data['telegram_bot_link'] = "https://t.me/baku_news_bot";
        $data['dimensions']        = [
            '1'  => '1D',
            '7'  => '7D',
            '30' => '30D',
        ];

        $data['dimension'] = request('dimension', '1');

        // 计算总净积分 - 所有用户完成任务的积分总和减去撤销的积分总和
        $total_points_raw = UserTask::whereIn('task_status', ['completed', 'revoked'])
            ->selectRaw("
                SUM(
                    CASE 
                        WHEN task_status = 'completed' THEN points
                        WHEN task_status = 'revoked' THEN -points
                        ELSE 0
                    END
                ) AS net_points
            ")
            ->first()
            ->net_points ?? 0;
        
        // 确保总积分不会为负数
        $total_points = max(0, $total_points_raw);
        
        $data['total_points'] = $total_points;

        // 从数据库获取真实的KPI数据（使用缓存以提高性能）
        $data['kpis'] = [
            [
                'label' => 'Communities Served',
                'value' => Cache::remember('kpi_communities_served', 300, function () { // 缓存5分钟
                    return \App\Models\TelegramChat::group()->count(); // 群组数量
                }),
            ],
            [
                'label' => 'Community Scouts',
                'value' => Cache::remember('kpi_community_scouts', 300, function () { // 缓存5分钟
                    return \App\Models\TelegramUser::count(); // 用户数量
                }),
            ],
            [
                'label' => 'Buzz news',
                'value' => Cache::remember('kpi_buzz_news', 300, function () { // 缓存5分钟
                    return \App\Models\Article::where('category', 'buzz_news')->count(); // 新闻数量
                }),
            ],
            [
                'label' => 'Reports Generated',
                'value' => Cache::remember('kpi_reports_generated', 300, function () { // 缓存5分钟
                    return \App\Models\Article::where('category', 'group_report')->count(); // 报告数量
                }),
            ],
        ];
        return view($view, $data);
    }
}
