<?php

namespace App\Http\Controllers\Admin;

use Merlion\Components\Dropdown;

class DashboardController
{
    public function __invoke()
    {
//        $row = Row::make()->class('g-3');
//        $row->column(
//            Metric::make()
//                ->link('/admin/agents')
//                ->color('primary')->icon(Icon::make()->icon('ri-robot-line'))->title(Agent::count())
//                ->sub_title(__('agent.label_plural')),
//            'col-lg-4 col-sm-6 col-12');
        $dropdown = Dropdown::make()->button(['icon' => 'ri-more-2-fill', 'sm' => true, 'color' => 'action'])
            ->actions([
                ['content' => __('merlion::base.edit'), 'link' => '/admin/agents/1/edit'],
            ]);
        return admin()->title(__('base.dashboard'))
            ->content($dropdown)
            ->render();
    }
}
