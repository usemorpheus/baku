<?php

namespace App\Http\Controllers\Api;

use App\Models\Agent;
use App\Models\Chat;
use App\Services\FastGPT;
use App\Telegram\Commands\Interview\Start;
use Exception;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramController
{
    /**
     * @throws TelegramSDKException
     */
    public function __invoke($code)
    {
        try {
            $agent = Agent::where('code', $code)->first();
            if (empty($agent)) {
                Log::error('Agent not found: ' . $code, request()->all());
                return 'ok';
            }
            request()->merge(['agent' => $agent]);
            Telegram::addCommands([
                Start::class,
            ]);
            $updates = Telegram::commandsHandler(true);
            if (empty($updates)) {
                return 'ok';
            }
            Log::debug($updates);

            $chat    = $updates->getChat();
            $message = $updates->getMessage();
            if (empty($message)) {
                return 'ok';
            }
            $text = $message->text ?? null;
            if (empty($text)) {
                return 'ok';
            }

            $fast_chat = Chat::where('agent_id', $agent->id)
                ->where('channel_id', $chat->id)
                ->where('channel', 'telegram')
                ->where('channel_user', $message->getFrom()->id)
                ->first();

            if (empty($fast_chat)) {
                $text = '我的昵称是:' . $message->getFrom()->last_name . ' ' . $message->getFrom()->first_name . "\n" . $text;
            }

            $fast_gpt         = new FastGPT($agent->url, $agent->api_key);
            $response         = $fast_gpt->send($text, $fast_chat?->fast_chat_id, $chat->username);
            $response_message = $response['choices'][0]['message']['content'] ?? 'error';
            Log::debug($response['id']);
            Telegram::sendMessage([
                'chat_id' => $chat->id,
                'text'    => $response_message,
            ]);
            Chat::updateOrCreate([
                'agent_id'     => $agent->id,
                'channel_id'   => $chat->id,
                'channel'      => 'telegram',
                'channel_user' => $message->getFrom()->id,
            ], [
                'fast_chat_id' => $response['id'],
            ]);
        } catch (Exception $e) {
            Log::error($e);
            return 'ok';
        }
        return 'ok';
    }
}
