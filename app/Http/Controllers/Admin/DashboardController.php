<?php

namespace App\Http\Controllers\Admin;

use Merlion\Components\Widgets\Metric;

class DashboardController
{
    public function __invoke()
    {
        $metric = Metric::make();
        return admin()->title(__('base.dashboard'))
            ->content($metric)
            ->render();
    }
}
