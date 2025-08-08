<?php

namespace App\Http\Controllers\Admin;

class DashboardController
{
    public function __invoke()
    {
        return admin()->title(__('base.dashboard'))
            ->content("Welcome")
            ->render();
    }
}
