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
     * 验证推文转发
     * 
     * @param string $tweetUrl 推文URL
     * @param string $userTwitterUsername 用户的Twitter用户名
     * @return bool
     */
    public function verifyRetweet(string $tweetUrl, string $userTwitterUsername): bool
    {
        try {
            // 检查是否配置了Twitter API凭据
            $bearerToken = config('services.twitter.bearer_token');
            
            if (!$bearerToken) {
                // 如果没有API凭据，返回false，表示需要手动验证
                return false;
            }
            
            // 从推文URL提取推文ID
            $tweetId = $this->extractTweetId($tweetUrl);
            
            if (!$tweetId) {
                return false;
            }
            
            // 检查推文是否为转发
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $bearerToken,
            ])->get("https://api.twitter.com/2/tweets/{$tweetId}?expansions=author_id&user.fields=username");
            
            if ($response->successful()) {
                $tweetData = $response->json();
                
                // 检查是否为转发（RT）
                if (isset($tweetData['data']['text']) && 
                    (str_starts_with($tweetData['data']['text'], 'RT @') || 
                     str_contains($tweetData['data']['text'], 'RT @'))) {
                    // 检查是否是目标用户转发的
                    if (isset($tweetData['includes']['users'][0]['username'])) {
                        $retweetedByUsername = $tweetData['includes']['users'][0]['username'];
                        return strtolower($retweetedByUsername) === strtolower($userTwitterUsername);
                    }
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