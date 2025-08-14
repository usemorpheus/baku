<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiVerifyMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('api-key');
        if (empty($token) || $token !== config('services.api.api_key')) {
            abort(401);
        }
        return $next($request);
    }
}
