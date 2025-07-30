<?php

namespace App\Actions;

use App\Library\Twitter;
use App\Models\SocialAccount;
use Exception;
use Lorisleiva\Actions\Concerns\AsAction;

class RefreshTwttierAccount
{
    use AsAction;

    /**
     * @throws Exception
     */
    public function handle(SocialAccount $account): void
    {
        $twitter = new Twitter();
        $twitter->withToken($account->access_token);

        $twitter_info = $twitter->me();
        if (!empty($twitter_info['data']['username'])) {
            $account->update([
                'name'     => $twitter_info['data']['name'],
                'username' => $twitter_info['data']['username'],
                'image'    => $twitter_info['data']['profile_image_url'],
            ]);
        }
    }
}
