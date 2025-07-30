<?php

namespace App\Actions;

use App\Library\Twitter;
use App\Models\Article;
use App\Models\SocialAccount;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;

class PullTwitterMentions
{
    use AsAction;

    public function handle()
    {
        $account = SocialAccount::where('platform', 'twitter')->first();
        if (empty($account)) {
            return null;
        }
        $twitter = new Twitter();
        if ($account->access_token_expired_at < now()) {
            RefreshTwttierToken::run($account);
        }
        $payload   = $account->payload ?? [];
        $newest_id = $payload['newest_id'] ?? null;

        $twitter->withToken($account->access_token);
        $response = $twitter->userMentions($account->account_id, [
            'since_id' => $newest_id,
        ]);

        if (empty($response['data'])) {
            return;
        }

        $messages = $response['data'];

        foreach ($messages as $message) {
            echo $message['text'];
            if (!empty($message['text'])) {
                $chat_response = HandleReportMessage::run($message['text'], $message['id'], [
                    'id'       => $account->id,
                    'username' => $account->username,
                ]);

                if ($chat_response instanceof Article) {
                    dump('快讯已经生成' . $chat_response->title);

                    // 上传图片
                    $media        = $twitter->upload([
                        'media_category' => 'tweet_image',
                        'media'          => base64_encode(file_get_contents($chat_response->image)),
                    ]);
                    $media_id     = $media['data']['id'] ?? null;
                    $twitter_post = $twitter->createPost([
                        'media' => [
                            'media_ids' => [$media_id],
                        ],
                        'reply' => [
                            'in_reply_to_tweet_id' => $message['id'],
                        ],
                    ]);

                    Log::debug('生成快讯');
                    Log::debug($twitter_post);
                }

                if (is_string($chat_response)) {
                    $twitter_post = $twitter->createPost([
                        'text'  => $chat_response,
                        'reply' => [
                            'in_reply_to_tweet_id' => $message['id'],
                        ],
                    ]);
                    Log::debug('进一步对话');
                    Log::debug($twitter_post);
                }
            }
        }

        if (!empty($response['meta']['newest_id'])) {
            $payload['newest_id'] = $response['meta']['newest_id'];
            $account->payload     = $payload;
            $account->save();
        } else {
            Log::debug($response);
        }
    }
}
