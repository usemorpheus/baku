<?php

namespace App\Http\Controllers\Admin;

use App\Models\Agent;
use Merlion\Components\Container\Row;
use Merlion\Components\Icon;
use Merlion\Components\Widgets\Metric;

class DashboardController
{
    public function __invoke()
    {
        $row = Row::make()->class('g-3');
        $row->column(
            Metric::make()
                ->link('/admin/agents')
                ->color('primary')->icon(Icon::make()->icon('ri-robot-line'))->title(Agent::count())
                ->sub_title(__('agent.label_plural')),
            'col-lg-4 col-sm-6 col-12');
        return admin()->title(__('base.dashboard'))
            ->content($row)
            ->render();
    }
}
