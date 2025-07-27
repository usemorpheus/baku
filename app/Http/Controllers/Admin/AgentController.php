<?php

namespace App\Http\Controllers\Admin;

use Merlion\Components\Button;
use Merlion\Components\Container\Card;
use Merlion\Components\Container\Flex;
use Merlion\Components\Container\Row;
use Merlion\Components\Icon;
use Merlion\Components\Layouts\Admin;
use Merlion\Components\Text;
use Merlion\Http\Controllers\CrudController;

class AgentController extends CrudController
{
    protected string $model = \App\Models\Agent::class;
    protected string $route = 'agents';
    protected string $lang = 'agent';

    public function index()
    {
        $agents = Row::make()->gap();
        foreach (\App\Models\Agent::all() as $agent) {
            $card = Card::make()
                ->header(Flex::make()->gap(1)
                    ->content([
                        Icon::make()->image($agent->image)->class('rounded maxh-36px shadow bg-lime-lt'),
                        Flex::make()->column()->gap(1)->content([
                            Text::make()->h3()->content($agent->name)->class('mb-0'),
                            '<span><i class="ri-checkbox-blank-circle-fill text-success"></i> Online</span>',
                        ]),
                    ]))
                ->body($agent->description);
            $agents->column($card, 'col-lg-4 col-sm-6 col-12');
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
                [
                    'name'     => 'name',
                    'required' => true,
                    'style'    => 'min-width: 288px;',
                    'rules'    => 'required|min:3|max:100',
                ],
                'url',
                'api_key',
                [
                    'name'    => 'status',
                    'type'    => 'select',
                    'options' => [
                        'success' => 'Online',
                        'danger'  => 'Offline',
                    ],
                ],
                [
                    'name' => 'image',
                    'type' => 'image',
                ],
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
