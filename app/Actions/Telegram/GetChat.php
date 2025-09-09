<?php


namespace App\Actions\Telegram;

use Illuminate\Support\Facades\Http;
use Lorisleiva\Actions\Concerns\AsAction;

class GetChat
{
    use AsAction;

    public function handle($chat_id, $token = null)
    {
        $token = $token ?: config('baku.telegram_bot_token');

        $response = Http::get(
            "https://api.telegram.org/bot{$token}/getChat",
            [
                'chat_id' => $chat_id,
            ]
        )->json();

        if ($response['ok'] ?? false) {
            return $response['result'];
        }
        return null;
    }
}
