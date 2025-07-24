<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Merlion\Components\Menu;
use Merlion\Components\Pages;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Pages\Login::$username      = 'email';
        Pages\Login::$usernameLabel = 'Email';

        admin()->serving(function () {
            admin()->brandName('Baku')
                ->menus([
                    Menu::make(label: 'Dashboard', link: '/admin', icon: 'ri-dashboard-line'),
                    Menu::make(label: __('agent.label_plural'), link: admin()->route('agents.index'),
                        icon: 'ri-robot-line'),
                    Menu::make(label: 'Chats', link: admin()->route('agents.index'),
                        icon: 'ri-message-3-line'),
                    Menu::make(label: __('article.label_plural'),
                        link: admin()->route('articles.index'),
                        icon: 'ri-article-line'
                    ),
                ]);
        });
    }
}
