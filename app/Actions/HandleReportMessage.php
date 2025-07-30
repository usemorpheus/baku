<?php

namespace App\Actions;

use App\Models\Agent;
use App\Models\Article;
use App\Models\Chat;
use App\Services\FastGPT;
use Lorisleiva\Actions\Concerns\AsAction;

class HandleReportMessage
{
    use AsAction;

    public function handle($text, $thread_id, $user)
    {
        $agent = Agent::where('code', 'baku_reporter')->first();
        if (empty($agent)) {
            return null;
        }

        $fast_chat = Chat::where('agent_id', $agent->id)
            ->where('channel_id', $thread_id)
            ->where('channel', 'twitter')
            ->where('channel_user', $user['id'])
            ->first();

        $fast_gpt = new FastGPT($agent->url, $agent->api_key);
        $response = $fast_gpt->send($text, $fast_chat?->fast_chat_id, $user['username']);

        if (empty($response['id'])) {
            return null;
        }

        Chat::updateOrCreate([
            'agent_id'     => $agent->id,
            'channel_id'   => $thread_id,
            'channel'      => 'twitter',
            'channel_user' => $user['id'],
        ], [
            'fast_chat_id' => $response['id'],
        ]);
        $content = $response['choices'][0]['message']['content'] ?? 'error';
        $data    = to_json($content);
        if (!empty($data['title']) && !empty($data['content'])) {
            $image = GenerateImage::run($data['title'], $data['content']);
            if (empty($image)) {
                return null;
            }
            return Article::create([
                'title'   => $data['title'],
                'content' => $data['content'],
                'image'   => $image,
                'data'    => [
                    'chat_id' => $response['id'],
                ],
            ]);
        }
        return $content;
    }
}
