<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 检查data列是否已存在，如果不存在则添加
        if (!Schema::hasColumn('telegram_users', 'data')) {
            Schema::table('telegram_users', function (Blueprint $table) {
                $table->json('data')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::table('telegram_users', function (Blueprint $table) {
            $table->dropColumn('data');
        });
    }
};