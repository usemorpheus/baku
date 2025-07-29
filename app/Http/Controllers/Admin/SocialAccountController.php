<?php

namespace App\Http\Controllers\Admin;


use App\Models\SocialAccount;
use Merlion\Components\Button;
use Merlion\Components\Layouts\Admin;
use Merlion\Http\Controllers\CrudController;

class SocialAccountController extends CrudController
{
    protected string $model = SocialAccount::class;
    protected string $route = 'social_accounts';
    protected string $label = 'Social account';

    protected function beforeIndexAddSocialLogin()
    {
        admin()->content(Button::make()->color('dark')->link(admin()->route('twitter.login'))
            ->label('Add twitter'), Admin::POSITION_HEADER_RIGHT);
    }

    public function schemas()
    {
        return [
            'columns' => [
                'name',
                'platform',
                'account_id',
                'access_token_expired_at',
            ],
        ];
    }
}
