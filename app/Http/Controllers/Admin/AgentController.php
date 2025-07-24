<?php

namespace App\Http\Controllers\Admin;

use App\Components\Agent;
use Merlion\Components\Button;
use Merlion\Components\Form\Errors;
use Merlion\Components\Form\Fields\Text;
use Merlion\Components\Form\Fields\Textarea;
use Merlion\Components\Form\Form;
use Merlion\Components\Layouts\Card;
use Merlion\Components\Layouts\Flex;

class AgentController
{
    public function index()
    {
        $agents = Flex::make()->wrap()->gap(3);
        foreach (\App\Models\Agent::all() as $agent) {
            $agents->content(Agent::make()->model($agent));
        }

        return admin()->title(__('agent.label_plural'))->content($agents)->render();
    }

    public function edit(\App\Models\Agent $agent)
    {
        admin()->title(__('merlion::base.edit') . ' ' . $agent->name)
            ->back(admin()->route('agents.index'));
        $card = Card::make();
        $card->body(Flex::make([
            Errors::make(),
            Text::make(name: "name", label: __('agent.name'))->value($agent->name),
            Text::make(name: "description", label: "描述")->value($agent->description),
            Textarea::make(name: "prompt", label: "提示词")->rows(5)->value($agent->prompt),
        ])->wrap()->column()->gap(3));
        $card->footer(Button::make()->primary()->label(__('merlion::base.save')));
        $form = Form::make($card)->put(admin()->route('agents.update', $agent));
        return admin()->content($form)->render();
    }

    public function update(\App\Models\Agent $agent)
    {
        request()->validate([
            'name' => 'required|min:2|max:100',
        ]);
        $agent->update(request()->all());
        admin()->success('Agent updated');
        return back();
    }
}
