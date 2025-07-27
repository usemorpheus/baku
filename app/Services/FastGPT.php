<?php

namespace App\Services;

use Http;
use Illuminate\Http\Client\ConnectionException;

class FastGPT
{
    public function __construct(
        public string $api_url,
        public string $api_key
    ) {
    }

    /**
     * @throws ConnectionException
     */
    public function send($message, $chat_id = null, $user = null)
    {
        return Http::withHeader('Authorization', 'Bearer ' . $this->api_key)
            ->post($this->api_url . '/v1/chat/completions', [
                'chatId'    => $chat_id,
                'messages'  => [
                    [
                        "content" => $message,
                        "role"    => "user",
                    ],
                ],
                "customUid" => $user,
            ])->json();
    }
}
