<?php

namespace App\Actions;

use App\Models\Metric;
use App\Models\TelegramChat;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateChatMetrics
{
    use AsAction;

    public static array $metrics = [
        'market_cap'           => Metrics\GetMarketCapMetric::class,
        'change'               => Metrics\GetMarketCapChangeMetric::class,
        'group_messages'       => Metrics\GetGroupMessagesMetric::class,
        'total_members'        => Metrics\GetTotalMembersMetric::class,
        'active_members'       => Metrics\GetActiveMembersMetric::class,
        'baku_interactions'    => Metrics\GetBakuInteractionsMetric::class,
        'builder_level'        => Metrics\GetBuilderLevelMetric::class,
        'community_activities' => Metrics\GetCommunityActivitiesMetric::class,
        'community_sentiment'  => Metrics\GetCommunitySentimentMetric::class,
        'key_builders'         => Metrics\GetKeyBuildersMetric::class,
        'voice_communications' => Metrics\GetVoiceCommunicationsMetric::class,
    ];

    public function handle($chat = null, $dimension = null): void
    {
        if (empty($chat)) {
            $chats = TelegramChat::group()->get();
            foreach ($chats as $chat) {
                $this->handle($chat, $dimension);
            }
            return;
        }

        $date  = date('Y-m-d');
        $year  = date('Y');
        $month = date('m');
        $day   = date('d');

        $metrics = [
            'year'  => $year,
            'month' => $month,
            'day'   => $day,
        ];

        if (empty($dimension)) {
            $dimensions = [1, 7, 30];
        } else {
            $dimensions = is_array($dimension) ? $dimension : [$dimension];
        }

        foreach ($dimensions as $dimension) {
            foreach (static::$metrics as $key => $handler) {
                $metrics[$key] = $handler::run($chat, $dimension);
            }

            Metric::updateOrCreate([
                'date'             => $date,
                'telegram_chat_id' => $chat->id,
                'dimension'        => $dimension,
            ], $metrics);
        }

    }
}
