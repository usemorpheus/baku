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
        Schema::create('telegram_messages', function (Blueprint $table) {
            $table->id();
            $table->string('message_id')->nullable();
            $table->string('telegram_chat_id')->nullable();
            $table->string('telegram_user_id')->nullable();
            $table->text('text')->nullable();
            $table->string('datetime')->nullable();
            $table->text('data')->nullable();
            $table->timestamps();
        });
    }

};
