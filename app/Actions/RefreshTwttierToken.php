<?php

namespace App\Actions;

use App\Library\Twitter;
use App\Models\SocialAccount;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Lorisleiva\Actions\Concerns\AsAction;

class RefreshTwttierToken
{
    use AsAction;

    /**
     * @throws ConnectionException
     * @throws Exception
     */
    public function handle(SocialAccount $account): void
    {
        $twitter = new Twitter();
        $token   = $twitter->refreshToken($account->refresh_token);

        if (!$token['access_token']) {
            throw new Exception('Fail to get access token');
        }
        $account->update([
            'access_token'            => $token['access_token'],
            'refresh_token'           => $token['refresh_token'],
            'access_token_expired_at' => date('Y-m-d H:i:s', time() + $token['expires_in']),
        ]);
    }
}
