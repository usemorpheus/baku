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

        // $date = Carbon::now()->format('Y-m-d');

        $data['metrics'] = Metric::with('chat')
            ->where('dimension', $dimension)
            // ->where('date', $date)
            ->orderBy('baku_index')
            ->paginate()
            ->appends(request()->except('page'));

        return $this->response('community', $data);
    }

    public function points()
    {
        $data = [
            'tab' => 'points',
        ];

        // 获取积分最高的用户排行榜
        // 按用户ID分组，计算每个用户的总积分
        $userPoints = UserTask::where('task_status', 'completed')
            ->groupBy('telegram_user_id')
            ->selectRaw('telegram_user_id, SUM(points) as total_points')
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

        // 计算总积分 - 所有用户完成任务的积分总和
        $total_points = UserTask::where('task_status', 'completed')->sum('points');
        $data['total_points'] = $total_points;

        $data['kpis'] = [
            [
                'label' => 'Communities Served',
                'value' => 720,
            ],
            [
                'label' => 'Community Scounts',
                'value' => 231,
            ],
            [
                'label' => 'Buzz news',
                'value' => 33,
            ],
            [
                'label' => 'Reports Generated',
                'value' => 23,
            ],
        ];
        return view($view, $data);
    }
}
