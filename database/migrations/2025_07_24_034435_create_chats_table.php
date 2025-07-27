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
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->foreignId('agent_id')->nullable();
            $table->foreignId('user_id')->nullable();
            $table->string('channel')->nullable();
            $table->string('channel_id')->nullable();
            $table->string('channel_user')->nullable();
            $table->string('fast_chat_id')->nullable();
            $table->text('settings')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};
