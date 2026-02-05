<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TelegramUser extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $guarded = [];

    protected $casts = [
        'id' => 'string',
        'is_activated' => 'boolean',
        'activated_at' => 'datetime',
    ];

    public function chats(): BelongsToMany
    {
        return $this->belongsToMany(TelegramChat::class, 'telegram_user_chat');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(TelegramMessage::class, 'telegram_user_id');
    }
}
