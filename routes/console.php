<?php

use App\Actions\UpdateBakuCommunity;

\Illuminate\Support\Facades\Schedule::call(
    UpdateBakuCommunity::class
)->dailyAt('00:00');
