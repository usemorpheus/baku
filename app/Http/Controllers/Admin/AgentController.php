<?php

namespace App\Http\Controllers\Admin;

use App\Components\Agent;
use Merlion\Components\Button;
use Merlion\Components\Flex;
use Merlion\Components\Form\Fields\Text;
use Merlion\Components\Layouts\Admin;
use Merlion\Http\Controllers\CrudController;

class AgentController extends CrudController
{
    protected string $model = \App\Models\Agent::class;
    protected string $route = 'agents';
    protected string $lang = 'agent';

    public function index()
    {
        $agents = Flex::make()->wrap()->gap(3);
        foreach (\App\Models\Agent::all() as $agent) {
            $agents->content(Agent::make()->model($agent));
        }
        admin()->content(
            Button::make()
                ->icon('ti ti-plus icon')
                ->primary()->link(admin()->route('agents.create'))->label(__('merlion::base.create')),
            Admin::POSITION_HEADER_RIGHT);
        return admin()->title(__('agent.label_plural'))->content($agents)->render();
    }

    protected function schemas()
    {
        return [
            'fields' => [
                Text::make(name: 'name')->label(__('agent.name')),
                [
                    'name'        => 'description',
                    'type'        => 'textarea',
                    'placeholder' => '输入简短描述',
                    'rows'        => function () {
                        return 10;
                    },
                    'full'        => true,
                ],
            ],
        ];
    }
}
