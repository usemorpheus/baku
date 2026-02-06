<?php

namespace App\Http\Controllers\Admin;

use App\Models\TaskType;
use Merlion\Http\Controllers\CrudController;

class TaskTypeController extends CrudController
{
    protected string $model = TaskType::class;
    protected string $route = 'task-types';

    protected function schemas(): array
    {
        return [
            'id' => [
                'showOn' => ['index', 'show'],
            ],
            'name' => [
                'filterable' => true,
                'sortable' => true,
            ],
            'title' => [
                'filterable' => true,
                'sortable' => true,
            ],
            'description' => [
                'type' => 'textarea',
                'hideFrom' => 'index',
            ],
            'points_reward' => [
                'type' => 'number',
                'filterable' => true,
            ],
            'verification_method' => [
                'type' => 'select',
                'options' => [
                    'auto' => 'Auto',
                    'manual' => 'Manual',
                    'api_check' => 'API Check',
                ],
                'filterable' => true,
            ],
            'requirements' => [
                'type' => 'textarea',
                'hideFrom' => 'index',
            ],
            'is_active' => [
                'type' => 'select',
                'options' => [
                    1 => 'Active',
                    0 => 'Inactive',
                ],
                'filterable' => true,
                'exact' => true,
            ],
            'created_at' => [
                'showOn' => ['index', 'show'],
                'sortable' => [
                    'created_at' => 'Oldest',
                    '-created_at' => [
                        'label' => 'Newest',
                        'default' => true,
                    ],
                ],
            ],
        ];
    }
}