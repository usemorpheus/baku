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
        admin()->serving(function () {
            admin()->script(<<<TAWK
var Tawk_API = Tawk_API || {}, Tawk_LoadStart = new Date();
    (function () {
        var s1 = document.createElement("script"), s0 = document.getElementsByTagName("script")[0];
        s1.async = true;
        s1.src = 'https://embed.tawk.to/68831b632f66bc191640cb7f/1j1025vec';
        s1.setAttribute('crossorigin', '*');
        s0.parentNode.insertBefore(s1, s0);
    })();
TAWK
);
            Pages\Login::$username      = 'username';
            Pages\Login::$usernameLabel = 'Username';

            Pages\Home::callback(function () {
                /** @var Pages\Home $this */
                admin()->title("Home");
                $this->content(Button::make()->primary()->xs()->label('View'));
            });
            admin()
                ->menus([
                    Menu::make(label: 'Dashboard', link: '/admin', icon: 'ti ti-home fs-2'),
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
