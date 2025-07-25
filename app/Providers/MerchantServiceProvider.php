<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Merlion\Components\Button;
use Merlion\Components\Menu;
use Merlion\Components\Pages;

class MerchantServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        admin('merchant')
            ->brandName("Merchant Name")
            ->serving(function () {
                Pages\Home::callback(function () {
                    admin()->title("Merchant");
                    /** @var Pages\Home $this */
                    $this->content(Button::make()->primary()->xs()->label('Merchant'));
                });

                admin('merchant')
                    ->menus([
                        Menu::make(label: 'Dashboard', link: '/merchant', icon: 'ri-dashboard-line'),
                    ]);
            });
    }
}
