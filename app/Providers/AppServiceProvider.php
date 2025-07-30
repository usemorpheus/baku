<?php

namespace App\Providers;

use App\Actions\PullTwitterMentions;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->commands([
            PullTwitterMentions::class,
        ]);
    }
}
