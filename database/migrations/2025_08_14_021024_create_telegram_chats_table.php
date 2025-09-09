<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('telegram_chats', function (Blueprint $table) {
            $table->string('id')->index();
            $table->string('status')->nullable();
            $table->string('title')->nullable();
            $table->string('type')->nullable();
            $table->string('invite_by')->nullable();
            $table->text('data')->nullable();
            $table->timestamps();
        });
    }
};
