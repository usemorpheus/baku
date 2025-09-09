<?php


namespace App\Actions\Telegram;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Lorisleiva\Actions\Concerns\AsAction;

class GetFile
{
    use AsAction;

    public function handle($file_id, $token = null)
    {
        $token = $token ?: config('baku.telegram_bot_token');

        $response = Http::get(
            "https://api.telegram.org/bot{$token}/getFile",
            [
                'file_id' => $file_id,
            ]
        )->json();

        if ($response['ok'] ?? false) {
            $file_path = $response['result']['file_path'];
            $url       = "https://api.telegram.org/file/bot{$token}/{$file_path}";
            $data      = file_get_contents($url);
            Storage::disk('public')->put('telegram/' . $file_path, $data);
            return Storage::disk('public')->url('telegram/' . $file_path);
        }
        return null;
    }
}
