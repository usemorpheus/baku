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
        'revoked_at' => 'datetime',
    ];
    
    /**
     * 获取有效积分（完成的返回正数，撤销的返回负数）
     */
    public function getEffectivePointsAttribute()
    {
        return $this->task_status === 'revoked' ? -$this->points : $this->points;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(TelegramUser::class, 'telegram_user_id', 'id');
    }

    public function taskType(): BelongsTo
    {
        return $this->belongsTo(TaskType::class, 'task_type_id', 'id');
    }
}