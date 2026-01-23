<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Metric;
use Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ActivityController
{
    public function community()
    {
        $data      = [
            'tab' => 'community',
        ];
        $dimension = request('dimension', '1');

        $date = Carbon::now()->format('Y-m-d');

        switch ($dimension) {
            case '1':
                $date = Carbon::now()->format('Y-m-d');
                break;
            case '7':
                $date = Carbon::now()->subDays(6)->format('Y-m-d');
                break;
            case '30':
                $date = Carbon::now()->subDays(29)->format('Y-m-d');
                break;
        }

        $data['metrics'] = Metric::where('date', '>=', $date)
            ->select([
                'date',
                DB::raw('ANY_VALUE(ranking_growth_rate) as ranking_growth_rate'),
                DB::raw('ANY_VALUE(builder_level) as builder_level'),
                DB::raw('ANY_VALUE(change) as change'),
                DB::raw('ANY_VALUE(market_cap) as market_cap'),
                DB::raw('ANY_VALUE(baku_index) as baku_index'),
                DB::raw('SUM(group_messages) as group_messages'),
                DB::raw('SUM(active_members) as active_members'),
                DB::raw("SUM(
                    CASE 
                        WHEN key_builders ~ '^[0-9]+$' 
                        THEN CAST(key_builders AS INTEGER)
                        ELSE 0 
                    END
                ) as key_builders"),
                DB::raw('SUM(baku_interactions) as baku_interactions'),
                DB::raw('SUM(community_activities) as community_activities'),
                DB::raw('SUM(voice_communications) as voice_communications'),
                DB::raw('AVG(community_sentiment) as community_sentiment'),
                // 添加 chat 相关字段
                DB::raw('(SELECT title FROM telegram_chats WHERE id = metrics.telegram_chat_id LIMIT 1) as chat_title'),
                DB::raw('ANY_VALUE(telegram_chat_id) as chat_id')
            ])
            ->groupBy('date')
            ->paginate()
            ->appends(request()->except('page'));

        return $this->response('community', $data);
    }

    public function points()
    {
        $data = [
            'tab' => 'points',
        ];

        $dimension       = request('dimension', '1');
        $data['metrics'] = Metric::with('chat')->where('dimension', $dimension)
            ->orderBy('baku_index')
            ->paginate()
            ->appends(request()->except('page'));

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

        $total_points = Cache::get('baku_total_points', rand(10000, 20000));
        $total_points += rand(0, 500);
        cache()->put('baku_total_points', $total_points);
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
