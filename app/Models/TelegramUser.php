<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TelegramUser extends Model
{
    public $incrementing = false;

    protected $guarded = [];

    public function chats(): BelongsToMany
    {
        return $this->belongsToMany(TelegramChat::class, 'telegram_user_chat');
    }
}
