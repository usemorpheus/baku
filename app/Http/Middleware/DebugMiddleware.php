<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\Facades\Log;

class DebugMiddleware extends Authenticate
{
    public function unauthenticated($request, array $guards)
    {
        Log::debug('Unauthenticated');
    }
}
