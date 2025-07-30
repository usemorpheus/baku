<?php

namespace App\Library;

use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use Http;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Str;

class Twitter
{

    protected string $api_key;
    protected string $api_secret;
    protected string $access_token;
    protected string $refresh_token;
    protected string $access_token_secret;
    protected string $bearer_token;
    protected string $api_version;
    protected string $api_url;

    protected array $headers = [];

    protected string $auth_mode;

    const string AUTH_MODE_APP  = 'app';  // app auth
    const string AUTH_MODE_USER = 'user'; // user context
    const string AUTH_MODE_DEV  = 'dev';  // developer context

    public function __construct()
    {
        $this->api_key      = config('services.twitter.api_key');
        $this->api_secret   = config('services.twitter.api_key');
        $this->api_version  = config('services.twitter.api_version');
        $this->bearer_token = config('services.twitter.bearer_token');
        $this->api_url      = config('services.twitter.api_url');

        $this->auth_mode = self::AUTH_MODE_APP;
    }

    public function withToken($token): static
    {
        $this->bearer_token = $token;
        return $this;
    }

    public function upload($data)
    {
        $this->auth_mode = self::AUTH_MODE_USER;
        return $this->post('media/upload', $data);
    }

    public function withRefreshToken($refresh_token): static
    {
        $this->refresh_token = $refresh_token;
        return $this;
    }

    public function me()
    {
        $this->auth_mode = self::AUTH_MODE_USER;
        return $this->get('users/me', [
            'user.fields' => 'id,name,username,profile_image_url',
        ]);
    }

    /**
     * @throws ConnectionException
     */
    public function createPost($data = [])
    {
        $this->auth_mode = self::AUTH_MODE_USER;
        return $this->post('tweets', $data);
    }

    /**
     * @throws ConnectionException
     */
    public function getPost($id)
    {
        $params = $this->getPostParameters();
        if (is_array($id)) {
            $url           = 'tweets';
            $params['ids'] = implode(',', $id);
        } else {
            $url = 'tweets/' . $id;
        }
        return $this->get($url, $params);
    }

    /**
     * @throws ConnectionException
     */
    public function userMentions($user_id, $params = [])
    {
        return $this->get('users/' . $user_id . '/mentions', array_merge($this->getPostParameters(), $params));
    }

    /**
     * @throws ConnectionException
     */
    public function usage()
    {
        $this->auth_mode = self::AUTH_MODE_DEV;
        return $this->get('usage/tweets', [
            'usage.fields' => 'cap_reset_day,daily_client_app_usage,daily_project_usage,project_cap,project_id,project_usage',
        ]);
    }

    /**
     * @throws ConnectionException
     */
    public function get($path, $params = [])
    {
        return $this->send('get', $path, $params);
    }

    protected function getPostParameters(): array
    {
        return [
            'tweet.fields' => "article,attachments,author_id,card_uri,community_id,conversation_id,created_at,display_text_range,edit_controls,edit_history_tweet_ids,entities,id,lang,media_metadata,note_tweet,possibly_sensitive,public_metrics,reply_settings,scopes,source,text,withheld",
            'poll.fields'  => "duration_minutes,end_datetime,id,options,voting_status",
            'expansions'   => "article.cover_media,article.media_entities,attachments.media_keys,attachments.media_source_tweet,author_id,edit_history_tweet_ids",
            "user.fields"  => "affiliation,connection_status,created_at,description,entities,id,location,most_recent_tweet_id,name,pinned_tweet_id,profile_banner_url,profile_image_url,protected,public_metrics,receives_your_dm,subscription_type,url,username,verified,verified_type,withheld",
            "media.fields" => "alt_text,duration_ms,height,media_key,preview_image_url,public_metrics,type,url,variants,width",
            "place.fields" => "contained_within,country,country_code,full_name,id,name,place_type",
        ];
    }

    public function getLoginUrl($callback_url): string
    {
        $state               = Str::random(40);
        $codeChallenge       = Str::random(64);
        $codeChallengeMethod = 'plain';

        session([
            'state'                 => $state,
            'code_challenge'        => $codeChallenge,
            'code_challenge_method' => $codeChallengeMethod,
        ]);

        $queryParams = [
            'response_type'         => 'code',
            'client_id'             => config('services.twitter.client_id'),
            'redirect_uri'          => $callback_url,
            'state'                 => $state,
            'code_challenge'        => $codeChallenge,
            'code_challenge_method' => $codeChallengeMethod,
            'scope'                 => 'offline.access tweet.read tweet.write users.read follows.read follows.write media.write',
        ];

        return 'https://twitter.com/i/oauth2/authorize?' . http_build_query($queryParams, '', '&', PHP_QUERY_RFC3986);
    }

    /**
     * @throws ConnectionException
     */
    public function getAccessToken($code, $redirect_uri)
    {
        $queryParams = [
            'code'          => $code,
            'grant_type'    => 'authorization_code',
            'redirect_uri'  => $redirect_uri,
            'code_verifier' => session('code_challenge'),
        ];

        $url = 'https://api.x.com/2/oauth2/token';

        return Http::asForm()
            ->withHeader('Authorization', 'Basic ' . $this->getOauthCredentials())
            ->post($url, $queryParams)
            ->json();
    }

    protected function getOauthCredentials(): string
    {
        $credentials = config('services.twitter.client_id') . ':' . config('services.twitter.client_secret');
        return base64_encode($credentials);
    }

    /**
     * @throws ConnectionException
     */
    public function refreshToken($code)
    {
        $queryParams = [
            'refresh_token' => $code,
            'grant_type'    => 'refresh_token',
        ];

        $url = 'https://api.x.com/2/oauth2/token';

        return Http::asForm()
            ->withHeader('Authorization', 'Basic ' . $this->getOauthCredentials())
            ->post($url, $queryParams)
            ->json();
    }

    /**
     * @throws ConnectionException
     */
    public function post($path, $data)
    {
        return $this->send('post', $path, $data);
    }

    /**
     * @throws ConnectionException
     */
    protected function send($method, $path, $data = [])
    {

        $stack   = HandlerStack::create();
        $options = [];

        if ($this->auth_mode == self::AUTH_MODE_APP) {
            $auth = new Oauth1([
                'consumer_key'    => config('services.twitter.api_key'),
                'consumer_secret' => config('services.twitter.api_secret'),
                'token'           => config('services.twitter.access_token'),
                'token_secret'    => config('services.twitter.access_token_secret'),
            ]);

            $options['auth'] = 'oauth';
        } else {
            $auth = new OauthBearer([
                'bearer' => $this->bearer_token,
            ]);
        }

        $stack->push($auth);

        $options['handler'] = $stack;

        $client = Http::withOptions($options);

        $url = $this->api_url . '/' . $this->api_version . '/' . $path;

        if ($method == 'get') {
            $url  .= '?' . http_build_query($data, '', '&', PHP_QUERY_RFC3986);
            $data = [];
        }

        if ($method == 'post') {
            $data = [
                'json' => $data,
            ];
        }

        $response = $client->send($method, $url, $data);
        return $response->json();
    }
}
