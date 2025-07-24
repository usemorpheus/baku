<?php

namespace App\Components;

use Merlion\Components\Renderable;

/**
 * @method $this model(\App\Models\Agent $model) Set model
 * @method \App\Models\Agent getModel() Get model
 */
class Agent extends Renderable
{
    protected string $view = 'components.agent';

    public mixed $name = '';
    public mixed $description = '';
    public \App\Models\Agent $model;
}
