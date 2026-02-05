<?php

namespace Database\Seeders;

use App\Models\TelegramChat;
use App\Models\User;
use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name'     => 'Test User',
            'password' => bcrypt('password'),
            'email'    => 'test@example.com',
        ]);

        // 运行任务类型种子
        $this->call(TaskTypesSeeder::class);
    }
}
