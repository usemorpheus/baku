<?php

use Illuminate\Support\Facades\Schedule;
use App\Actions\UpdateBakuCommunity;
use App\Actions\CalculateRankings;

// 每日更新Baku社区数据
Schedule::call(function () {
    UpdateBakuCommunity::run();
})->dailyAt('00:00');

// 每日计算排名
Schedule::call(function () {
    CalculateRankings::run();
})->dailyAt('00:05'); // 在更新数据后5分钟计算排名
