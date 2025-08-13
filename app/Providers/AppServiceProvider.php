<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Merlion\Components\Layouts\Admin;
use Merlion\Components\Menu;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            return true;
        });

        Admin::serving(function () {
            admin()->menus([
                Menu::make('dashboard', 'Dashboard')->icon('ti ti-home icon')->link('/admin'),
                Menu::make('articles', 'Articles')->icon('ti ti-news icon')->link('/admin/articles'),
            ]);
        });
    }
}
