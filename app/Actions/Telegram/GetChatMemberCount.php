<?php


namespace App\Actions\Telegram;

use Illuminate\Support\Facades\Http;
use Lorisleiva\Actions\Concerns\AsAction;

class GetChatMemberCount
{
    use AsAction;

    public function handle($chat_id, $token)
    {
        $response = Http::get(
            "https://api.telegram.org/bot{$token}/getChatMemberCount",
            [
                'chat_id' => $chat_id,
            ]
        )->json();

        if ($response['ok'] ?? false) {
            return $response['result'];
        }

        return 0;
    }
}
