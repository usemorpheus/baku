<?php
return [
    'telegram_bot_token' => env('TELEGRAM_BOT_TOKEN'),
    /** 需要加入的官方频道（用于 Join Telegram Channel 任务验证） */
    'telegram_channel_username' => env('TELEGRAM_CHANNEL_USERNAME', 'bakubuilders'),
];
