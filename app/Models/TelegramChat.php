<?php

namespace App\Models;

use App\Models\Traits\Metable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TelegramChat extends Model
{
    use Metable;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $guarded = [];
    protected $casts = [
        'id' => 'string',
    ];

    protected function getMetaField(): string
    {
        return 'data';
    }

    public function scopeGroup($query)
    {
        return $query->whereIn('type', ['group', 'supergroup']);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(TelegramMessage::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(TelegramUser::class, 'telegram_user_chat');
    }
}
