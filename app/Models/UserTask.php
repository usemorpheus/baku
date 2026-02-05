<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserTask extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'telegram_user_id',
        'task_type_id',
        'task_status',
        'points',
        'task_data',
        'completed_at',
        'verified_at'
    ];

    protected $casts = [
        'task_data' => 'array',
        'completed_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(TelegramUser::class, 'telegram_user_id', 'id');
    }

    public function taskType(): BelongsTo
    {
        return $this->belongsTo(TaskType::class, 'task_type_id', 'id');
    }
}