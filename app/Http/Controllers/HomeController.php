<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Setting;

class HomeController
{
    public function __invoke()
    {
        $webchat_link      = "https://play.baku.builders/chat/share?shareId=pJaog9I66rW1x2L8JTkHZFjL";
        $twitter_link      = "https://x.com/Baku_agent";
        $telegram_bot_link = "https://t.me/baku_news_bot";
        $news_link         = route('articles.index');

        $chat_links = [];

        for ($index = 0; $index <= 4; $index++) {
            $chat_links[] = [
                'label' => Setting::get('baku_text_' . $index, ''),
                'link'  => Setting::get('baku_link_' . $index, ''),
            ];
        }

        return view('home', compact('webchat_link', 'twitter_link', 'telegram_bot_link', 'news_link', 'chat_links'));
    }
}
