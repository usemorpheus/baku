<?php

namespace App\Http\Controllers\Webhooks;

use Illuminate\Support\Facades\Log;

class ZealyController
{
    public function __invoke()
    {
        $data = request()->all();

        Log::debug($data);

        
        return $data;
    }
}
