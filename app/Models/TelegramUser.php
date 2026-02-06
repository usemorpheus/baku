<?php

namespace App\Models;

use App\Models\Traits\Metable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TelegramUser extends Model
{
    use Metable;
    
    public $incrementing = false;
    protected $keyType = 'string';

    protected $guarded = [];
    
    protected $fillable = [
        'id', 'first_name', 'username', 'is_activated', 'activated_at', 'data'
    ];

    protected $casts = [
        'id' => 'string',
        'is_activated' => 'boolean',
        'activated_at' => 'datetime',
        'data' => 'array',
    ];

    protected function getMetaField(): string
    {
        return 'data';
    }

    public function chats(): BelongsToMany
    {
        return $this->belongsToMany(TelegramChat::class, 'telegram_user_chat');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(TelegramMessage::class, 'telegram_user_id');
    }
}
