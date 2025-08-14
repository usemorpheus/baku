<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Merlion\Components\Layouts\Admin;
use Merlion\Components\Menu;
use Opcodes\LogViewer\Facades\LogViewer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot(): void
    {

        LogViewer::auth(function ($request) {
            return true;
        });

        Gate::before(function ($user, $ability) {
            return true;
        });

        Admin::serving(function () {
            admin()->brandLogo(asset('images/baku/logo.png'));
            admin()->menus([
                Menu::make('dashboard', 'Dashboard')->icon('ti ti-home icon')->link('/admin'),
                Menu::make('articles', 'Articles')->icon('ti ti-news icon')->link('/admin/articles'),
                Menu::make('telegram', 'Telegram')->icon('ti ti-brand-telegram icon')
                    ->content([
                        Menu::make('chat', 'Chats')->link('/admin/telegram-chats'),
                        Menu::make('user', 'Users')->link('/admin/telegram-users'),
                        Menu::make('message', 'Messages')->link('/admin/telegram-messages'),
                    ]),
            ]);
        });
    }
}
