<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('telegram_users', function (Blueprint $table) {
            $table->string('id')->index();
            $table->string('first_name')->nullable();
            $table->string('username')->nullable();
            $table->string('avatar')->nullable();
            $table->boolean('is_bot')->nullable();
            $table->string('language')->nullable();
            $table->timestamps();
        });

        Schema::create('telegram_user_chat', function (Blueprint $table) {
            $table->id();
            $table->string('telegram_user_id')->nullable();
            $table->string('telegram_chat_id')->nullable();
            $table->text('data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telegram_users');
    }
};
