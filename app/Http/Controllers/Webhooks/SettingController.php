<?php

namespace App\Http\Controllers\Webhooks;

use App\Models\Setting;

class SettingController
{
    public function show($key)
    {
        return [
            'value' => Setting::where('key', $key)->first()->value ?? null,
        ];
    }

    public function update($key)
    {
        $value = request('value');

        Setting::updateOrCreate([
            'key' => $key,
        ], [
            'value' => $value,
        ]);

        return $value;
    }
}
