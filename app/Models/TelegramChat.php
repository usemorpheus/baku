<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TelegramChat extends Model
{
    protected $guarded = [];

    public function messages(): HasMany
    {
        return $this->hasMany(TelegramMessage::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(TelegramUser::class, 'telegram_user_chat');
    }
}
