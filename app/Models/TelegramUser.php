<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TelegramUser extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $guarded = [];

    protected $casts = [
        'id' => 'string',
    ];

    public function chats(): BelongsToMany
    {
        return $this->belongsToMany(TelegramChat::class, 'telegram_user_chat');
    }
}
