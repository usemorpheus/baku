<?php

namespace App\Http\Controllers\Admin;

use App\Models\Metric;
use App\Models\TelegramChat;
use Merlion\Http\Controllers\CrudController;

class MetricController extends CrudController
{
    protected string $model = Metric::class;

    protected function schemas(): array
    {
        return [
            'telegram_chat_id' => [
                'type'       => 'select',
                'options'    => TelegramChat::group()->pluck('title', 'id')->toArray(),
                'filterable' => true,
            ],
            'date'             => [
                'filterable'  => true,
                'inputType' => 'date',
                'class'       => 'text-nowrap',
            ],
            'dimension'        => [
                'type'       => 'select',
                'filterable' => true,
                'options'    => [
                    '1'  => '1D',
                    '7'  => '7D',
                    '30' => '30D',
                ],
            ],
            'baku_index'       => [
                'sortable' => true,
            ],
            'market_cap',
            'change',
            'group_messages',
            'total_members',
            'active_members',
            'baku_interactions',
            'builder_level',
            'community_activities',
            'community_sentiment',
            'key_builders',
            'voice_communications',
        ];
    }
}
