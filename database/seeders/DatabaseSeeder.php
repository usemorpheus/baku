<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use App\Models\Agent;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {

        AdminUser::factory()->create([
            'username' => 'root',
            'name'     => 'Admin User',
        ]);

        User::factory()->create([
            'name'  => 'Test User',
            'email' => 'test@example.com',
        ]);

        Agent::factory()->create([
            'name'        => 'twitter baku短讯海报正式版',
            'description' => '一个优秀的web3社区记者，支持所有人自动生成自助的web3新闻短讯。你对web3行业，sol bsc base公链非常了解',
            'code'        => 'baku_reporter',
            'url'         => 'https://play.baku.builders/api',
            'api_key'     => 'fastgpt-yRjWPjTBE7iTcgp6emPT9la5mCyxF4Eu5GjlP8cMRp0gO0ks1GNA',
        ]);

        Agent::factory()->create([
            'name'        => '1V1记者galin版',
            'description' => '我是 Baku, 一个 web3 记者，可以进行采访，并整理采访稿件，和发布稿件。',
            'code'        => 'baku_1v1',
            'url'         => 'https://play.baku.builders/api',
            'api_key'     => 'fastgpt-q72uqkuPfpHHSAgf9EAn8axWWMgrxIv9YbYxUoGODjGzaQccTd3ojPUzYH',
        ]);
    }
}
