<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Merlion\Components\Button;
use Merlion\Components\Menu;
use Merlion\Components\Pages;

class AdminServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../../routes/admin.php');
        admin()->serving(function () {
            Pages\Login::$username      = 'username';
            Pages\Login::$usernameLabel = 'Username';

            Pages\Home::callback(function () {
                /** @var Pages\Home $this */
                admin()->title("Home");
                $this->content(Button::make()->primary()->xs()->label('View'));
            });
            admin()->cspNonce(app('csp-nonce'));
            admin()->brandLogo(asset('/images/logo.png'));
            admin()
                ->menus([
                    Menu::make(label: __('base.dashboard'), link: '/admin', icon: 'ti ti-home fs-2'),
                    Menu::make(label: __('agent.label_plural'), link: admin()->route('agents.index'),
                        icon: 'ti ti-robot-face fs-2'),
                    Menu::make(label: 'Chats', link: admin()->route('agents.index'),
                        icon: 'ti ti-message fs-2'),
                    Menu::make(label: __('article.label_plural'),
                        link: admin()->route('articles.index'),
                        icon: 'ti ti-news fs-2'
                    ),
                ]);
        });
    }
}
