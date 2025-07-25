<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use App\Models\Agent;
use App\Models\Article;
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

        Article::factory(20)->create();
        Agent::factory(2)->create();
    }
}
