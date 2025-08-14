<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TelegramMessage extends Model
{

    protected $guarded = [];

    public function chat(): BelongsTo
    {
        return $this->belongsTo(TelegramChat::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(TelegramUser::class);
    }
}
