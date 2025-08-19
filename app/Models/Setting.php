<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $guarded = [];

    public static function get($key, $default = null)
    {
        $model = static::firstWhere('key', $key);

        return $model?->value ?? $default;
    }
}
