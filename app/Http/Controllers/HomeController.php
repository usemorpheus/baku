<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Metric;
use App\Models\Setting;
use Cache;

class HomeController
{
    public function __invoke()
    {
        $twitter_link      = "https://x.com/Baku_builders";
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
}
