<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TaskType;

class TaskTypesSeeder extends Seeder
{
    public function run()
    {
        // 清除旧数据
        TaskType::truncate();

        // 创建任务类型
        TaskType::create([
            'name' => 'add_bot_to_group',
            'title' => 'Add Bot to Group',
            'description' => 'Add @baku_news_bot to your Telegram group',
            'points_reward' => 100,
            'verification_method' => 'auto',
            'requirements' => json_encode(['bot_added' => true]),
            'is_active' => true,
        ]);

        TaskType::create([
            'name' => 'follow_twitter',
            'title' => 'Follow Twitter',
            'description' => 'Follow Baku official Twitter account: @Baku_builders',
            'points_reward' => 100,
            'verification_method' => 'api_check',
            'requirements' => json_encode(['twitter_username' => 'Baku_builders']),
            'is_active' => true,
        ]);

        TaskType::create([
            'name' => 'join_telegram_channel',
            'title' => 'Join Telegram Channel',
            'description' => 'Join the official Telegram channel @bakubuilders',
            'points_reward' => 50,
            'verification_method' => 'api_check',
            'requirements' => json_encode(['channel_username' => 'bakubuilders']),
            'is_active' => true,
        ]);

        TaskType::create([
            'name' => 'retweet_post',
            'title' => 'Retweet Post',
            'description' => 'Retweet the pinned tweet from @Baku_builders on Twitter/X',
            'points_reward' => 75,
            'verification_method' => 'api_check',
            'requirements' => json_encode(['must_retweet_from' => 'Baku_builders']),
            'is_active' => true,
        ]);
    }
}