<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Setting;
use Cache;

class HomeController
{
    public function __invoke()
    {
        $twitter_link      = "https://x.com/Baku_agent";
        $telegram_bot_link = "https://t.me/baku_news_bot";
        $news_link         = route('articles.index');
        $chat_links        = [];

        for ($index = 0; $index <= 4; $index++) {
            $chat_links[] = [
                'label' => Setting::get('baku_text_' . $index, ''),
                'link'  => Setting::get('baku_link_' . $index, ''),
            ];
        }
        return view('home', compact('twitter_link', 'telegram_bot_link', 'news_link', 'chat_links'));
    }

    public function activity()
    {
        $twitter_link      = "https://x.com/Baku_agent";
        $telegram_bot_link = "https://t.me/baku_news_bot";

        $tab = request('tab', 'ranking');

        $categories = [
            '1d'  => '1D',
            '7d'  => '7D',
            '30d' => '30D',
        ];
        $category   = request('category', '1d');

        $total_points = Cache::get('baku_total_points', rand(10000, 20000));
        $total_points += rand(0, 500);
        cache()->put('baku_total_points', $total_points);

        $kpis = [
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
        return view('activity',
            compact('twitter_link', 'categories', 'total_points', 'tab', 'kpis', 'category', 'telegram_bot_link'));
    }
}
