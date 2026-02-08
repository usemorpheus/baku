<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TwitterVerificationService
{
    private $apiKey;
    private $apiSecret;
    private $accessToken;
    private $accessSecret;

    public function __construct()
    {
        // 所有凭据从 .env 经 config('services.twitter.*') 读取
        $this->apiKey = config('services.twitter.client_id');
        $this->apiSecret = config('services.twitter.client_secret');
        $this->accessToken = config('services.twitter.access_token');
        $this->accessSecret = config('services.twitter.access_secret');
    }

    /**
     * 验证用户是否关注了指定的Twitter账户
     *
     * @param string $followerUsername 跟随者用户名
     * @param string $targetUsername 目标账户用户名（例如 'Baku_builders'）
     * @return bool
     */
    public function verifyFollow(string $followerUsername, string $targetUsername): bool
    {
        try {
            $bearerToken = config('services.twitter.bearer_token');
            if (empty($bearerToken)) {
                return false;
            }

            $followerUsername = trim($followerUsername);
            $targetUsername = trim($targetUsername);
            if ($followerUsername === '' || $targetUsername === '') {
                return false;
            }

            // Twitter API v2：GET users/by/username 使用 Bearer Token 可用
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $bearerToken,
            ])->get("https://api.twitter.com/2/users/by/username/{$followerUsername}");
            
            if ($response->successful()) {
                $userData = $response->json();
                $userId = $userData['data']['id'];
                
                $followResponse = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $bearerToken,
                ])->get("https://api.twitter.com/2/users/by/username/{$targetUsername}");
                
                if ($followResponse->successful()) {
                    $targetUserData = $followResponse->json();
                    $targetUserId = $targetUserData['data']['id'];
                    
                    // 检查关注关系（此接口需要 User Context，仅 Bearer Token 可能返回 403，届时走手动验证）
                    $checkResponse = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $bearerToken,
                    ])->get("https://api.twitter.com/2/users/{$userId}/following?user.fields=id,username&max_results=1000");

                    if ($checkResponse->successful()) {
                        $followingData = $checkResponse->json();
                        if (isset($followingData['data'])) {
                            foreach ($followingData['data'] as $following) {
                                if ($following['id'] === $targetUserId) {
                                    return true;
                                }
                            }
                        }
                    }
                }
            }
            
            return false;
            
        } catch (\Exception $e) {
            \Log::error('Twitter verification error: ' . $e->getMessage());
            // 在出错时也返回false，让用户手动验证
            return false;
        }
    }
    
    /**
     * 获取指定用户的置顶推文 ID（Twitter API v2）
     */
    public function getPinnedTweetId(string $username): ?string
    {
        $bearerToken = config('services.twitter.bearer_token');
        if (empty($bearerToken)) {
            return null;
        }
        $username = trim($username);
        if ($username === '') {
            return null;
        }
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $bearerToken,
            ])->get('https://api.twitter.com/2/users/by/username/' . $username, [
                'user.fields' => 'pinned_tweet_id',
            ]);
            if (!$response->successful()) {
                return null;
            }
            $data = $response->json();
            $pinnedId = $data['data']['pinned_tweet_id'] ?? null;
            return $pinnedId ? (string) $pinnedId : null;
        } catch (\Throwable $e) {
            \Log::error('Twitter getPinnedTweetId error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * 验证推文转发
     * 当 $requiredPinnedFromUsername 非空时，要求转发的必须是该账号的置顶推文。
     *
     * @param string $tweetUrl 用户提交的推文 URL（应为用户自己的 RT 链接）
     * @param string $userTwitterUsername 用户的 Twitter 用户名
     * @param string|null $requiredPinnedFromUsername 必须转发的账号（其置顶推文），如 'Baku_builders'
     * @return bool
     */
    public function verifyRetweet(string $tweetUrl, string $userTwitterUsername, ?string $requiredPinnedFromUsername = null): bool
    {
        try {
            $bearerToken = config('services.twitter.bearer_token');
            if (empty($bearerToken)) {
                return false;
            }

            $tweetId = $this->extractTweetId($tweetUrl);
            if (!$tweetId) {
                return false;
            }

            $pinnedTweetId = null;
            if ($requiredPinnedFromUsername !== null && trim($requiredPinnedFromUsername) !== '') {
                $pinnedTweetId = $this->getPinnedTweetId(trim($requiredPinnedFromUsername));
                if ($pinnedTweetId === null) {
                    return false;
                }
            }

            // 请求推文时带上 referenced_tweets 与 author_id，用于判断是否为 RT 以及原推 id、作者
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $bearerToken,
            ])->get("https://api.twitter.com/2/tweets/{$tweetId}", [
                'tweet.fields' => 'referenced_tweets,author_id',
                'expansions' => 'author_id',
                'user.fields' => 'username',
            ]);

            if (!$response->successful()) {
                return false;
            }

            $tweetData = $response->json();
            $tweet = $tweetData['data'] ?? null;
            if (!$tweet) {
                return false;
            }

            // 1) 必须是转发：存在 type=retweeted 的 referenced_tweets
            $referenced = $tweet['referenced_tweets'] ?? [];
            $retweetedId = null;
            foreach ($referenced as $ref) {
                if (($ref['type'] ?? '') === 'retweeted') {
                    $retweetedId = $ref['id'] ?? null;
                    break;
                }
            }
            if ($retweetedId === null) {
                return false;
            }

            // 2) 若要求置顶推文，则被转发的推文 ID 必须等于该账号的置顶推文 ID
            if ($pinnedTweetId !== null && $retweetedId !== $pinnedTweetId) {
                return false;
            }

            // 3) 转发者必须是该用户（author_id 对应 username）
            $authorId = $tweet['author_id'] ?? null;
            if (!$authorId) {
                return false;
            }
            $users = $tweetData['includes']['users'] ?? [];
            foreach ($users as $u) {
                if (($u['id'] ?? '') === $authorId) {
                    $authorUsername = $u['username'] ?? '';
                    return strtolower($authorUsername) === strtolower(trim($userTwitterUsername));
                }
            }

            return false;
        } catch (\Exception $e) {
            \Log::error('Twitter retweet verification error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * 从推文URL提取推文ID
     * 
     * @param string $tweetUrl
     * @return string|null
     */
    private function extractTweetId(string $tweetUrl): ?string
    {
        $tweetUrl = trim($tweetUrl);
        // 支持 twitter.com 与 x.com
        if (preg_match('/twitter\.com\/[^\/]+\/status\/(\d+)/', $tweetUrl, $m)) {
            return $m[1];
        }
        if (preg_match('/x\.com\/[^\/]+\/status\/(\d+)/', $tweetUrl, $m)) {
            return $m[1];
        }
        return null;
    }
}