<?php

namespace App\Http\Controllers\Webhooks;

use Illuminate\Support\Facades\Log;

class ZealyController
{
    public function __invoke()
    {
        $data = request()->all();
        $apiKey = request()->header('x-api-key');
        $zealyApiKey = env('ZEALY_API_KEY', '');

        Log::debug($data);

        if($apiKey !== $zealyApiKey){
            return response()->json(['message' => 'Invalid API key'], 400);
        }

        
        return response()->json(['message' => 'Invalid API key'], 400);
    }

    public function verifyTask()
    {
        $data = request()->all();
        $apiKey = request()->header('x-api-key');
        $zealyApiKey = env('ZEALY_API_KEY', '');

        Log::debug($data);

        if($apiKey !== $zealyApiKey){
            return response()->json(['message' => 'Invalid API key'], 400);
        }

        
        return $data;
    }
}
