<?php

namespace App\Http\Controllers\Admin;

use App\Models\Agent;
use Merlion\Components\Button;
use Merlion\Components\Container\Card;
use Merlion\Components\Container\Flex;
use Merlion\Components\Container\Row;
use Merlion\Components\Dropdown;
use Merlion\Components\Icon;
use Merlion\Components\Layouts\Admin;
use Merlion\Components\Text;
use Merlion\Http\Controllers\CrudController;

class AgentController extends CrudController
{
    protected string $model = Agent::class;
    protected string $route = 'agents';
    protected string $lang = 'agent';

    public function index()
    {
        $agents = Row::make()->gap();
        foreach (Agent::latest()->get() as $agent) {
            $agents->column($this->getAgentItem($agent), 'col-lg-4 col-sm-6 col-12');
        }
        admin()->content(
            Button::make()
                ->icon('ti ti-plus icon')
                ->primary()->link(admin()->route('agents.create'))->label(__('merlion::base.create')),
            Admin::POSITION_HEADER_RIGHT);
        return admin()->title(__('agent.label_plural'))->content($agents)->render();
    }

    protected function getAgentItem($agent)
    {
        $card = Card::make()
            ->header([
                Icon::make()->image($agent->image)->class('rounded maxh-36px shadow bg-lime-lt'),
                Flex::make()->column()->gap(1)->content([
                    Text::make()->h3()->content($agent->name)
                        ->class('mb-0')
                        ->link(admin()->route('agents.edit', $agent)),
                    '<span><i class="ri-checkbox-blank-circle-fill text-success"></i> Online</span>',
                ]),
                Dropdown::make()->class('card-actions')
                    ->button([
                        'icon'  => 'ri-more-2-fill',
                        'color' => 'action',
                    ])
                    ->actions([
                        ['content' => __('merlion::base.edit'), 'link' => '/admin/agents/' . $agent->id . '/edit'],
                    ]),
            ])
            ->body($agent->description);
        $card->getHeader()->gap(3)->alignItems();
        return $card;
    }

    protected function schemas()
    {
        return [
            'fields'  => [
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
            'actions' => ['view'],
        ];
    }
}
