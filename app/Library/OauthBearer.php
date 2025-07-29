<?php

namespace App\Library;

use Closure;

class OauthBearer
{

    protected string $bearer;

    public function __construct(array $config)
    {
        $this->bearer = $config['bearer'] ?? null;
    }

    public function __invoke(callable $handler): Closure
    {
        return function ($request, array $options) use ($handler) {
            $request = $request->withHeader('Authorization', 'Bearer ' . $this->bearer);
            return $handler($request, $options);
        };
    }
}
