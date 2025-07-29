<?php

namespace App\Http\Controllers\Admin;

use App\Library\Twitter;
use App\Models\SocialAccount;

class TwitterController
{

    public function login()
    {
        $twitter = new Twitter();
        return redirect($twitter->getLoginUrl(admin()->route('twitter.callback')));
    }

    public function callback()
    {
        $twitter = new Twitter();
        $token   = $twitter->getAccessToken(request('code'), admin()->route('twitter.callback'));
        if (empty($token['access_token'])) {
            admin()->danger('Twitter login fail');
            return back();
        }
        $twitter->withToken($token['access_token']);
        $twitter_user = $twitter->me();
        if (!empty($data = $twitter_user['data'] ?? null)) {
            $social_user = SocialAccount::updateOrCreate([
                'platform'   => 'twitter',
                'account_id' => $data['id'],
            ], [
                'name'                    => $data['name'],
                'username'                => $data['username'],
                'access_token'            => $token['access_token'],
                'refresh_token'           => $token['refresh_token'],
                'access_token_expired_at' => date('Y-m-d H:i:s', time() + $token['expires_in']),
            ]);
            admin()->toast('Twitter login success');
            return redirect(admin()->route('social_accounts.index'));
        }
        return $token;
    }

    public function refresh()
    {

    }
}
