<?php

use App\Actions\UpdateBakuCommunity;

\Illuminate\Support\Facades\Schedule::call(
    UpdateBakuCommunity::class
)->everyMinute();
