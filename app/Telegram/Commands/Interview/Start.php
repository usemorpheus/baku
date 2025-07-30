<?php

namespace App\Telegram\Commands\Interview;

use App\Models\Chat;
use Telegram\Bot\Commands\Command;

class Start extends Command
{
    protected string $pattern = 'start';
    protected string $name = 'start';
    protected string $description = 'Start new chat';

    public function handle(): void
    {
        $agent = request()->agent;
        // clear chat id
        $chat    = $this->getUpdate()->getChat();
        $message = $this->getUpdate()->getMessage();

        $fast_chat = Chat::where('agent_id', $agent->id)
            ->where('channel_id', $chat->id)
            ->where('channel', 'telegram')
            ->where('channel_user', $message->getFrom()->id)
            ->first();

        if ($fast_chat) {
            $fast_chat->delete();
        }
    }
}
