<?php

namespace App\Tools;

use Prism\Prism\Facades\Tool;

class Weather
{
    public static function make(): \Prism\Prism\Tool
    {
        return Tool::as('weather')
            ->for('Get current weather conditions')
            ->withStringParameter('city', 'The city to get weather for')
            ->using(function (string $city): string {
                return " {$city} 晴转多云, 最高温度 15度, 最低温度 30 度, 局部地区大雪, 偶尔伴有冰雹和海啸 。";
            });
    }
}
