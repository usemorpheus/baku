<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use Merlion\Http\Controllers\CrudController;

class SettingController extends CrudController
{
    protected string $model = Setting::class;

    protected function schemas(): array
    {
        return [
            'key'   => [
                'filterable' => true,
            ],
            'value' => [
                'type' => 'textarea',
                'full' => true,
            ],
        ];
    }
}
