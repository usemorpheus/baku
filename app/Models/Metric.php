<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Metric extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $fillable = [
        'telegram_chat_id',
        'date',
        'year',
        'month',
        'day',
        'dimension',
        'market_cap',
        'change',
        'group_messages',
        'total_members',
        'active_members',
        'key_builders',
        'builder_level',
        'baku_interactions',
        'community_activities',
        'voice_communications',
        'community_sentiment',
        'ranking_growth_rate',
        'baku_score',
        'baku_index',
        'meta',
        'created_at',
        'updated_at',
    ];

    /**
     * 自动转换数据类型
     */
    protected $casts = [
        'change' => 'float',
        'group_messages' => 'integer',
        'total_members' => 'integer',
        'active_members' => 'integer',
        'builder_level' => 'integer',
        'baku_interactions' => 'integer',
        'community_activities' => 'integer',
        'voice_communications' => 'integer',
        'community_sentiment' => 'integer',
        'ranking_growth_rate' => 'integer',
        'baku_score' => 'integer',
        'baku_index' => 'integer',
    ];

    public function chat(): BelongsTo
    {
        return $this->belongsTo(TelegramChat::class, 'telegram_chat_id');
    }
}
