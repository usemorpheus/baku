<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Metric;
use Cache;

class ActivityController
{
    public function community()
    {
        $data      = [
            'tab' => 'community',
        ];
        $dimension = request('dimension', '1');

        $data['metrics'] = Metric::with('chat')->where('dimension', $dimension)
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
