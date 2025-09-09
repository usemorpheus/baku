<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('metrics', function (Blueprint $table) {
            $table->id();
            $table->string('telegram_chat_id');
            $table->date('date');
            $table->integer('year');
            $table->integer('month');
            $table->integer('day');
            $table->integer('dimension')->default(1);
            $table->string('market_cap')->nullable();
            $table->decimal('change')->nullable();
            $table->integer('group_messages')->nullable();
            $table->integer('total_members')->nullable();
            $table->integer('active_members')->nullable();
            $table->text('key_builders')->nullable();
            $table->integer('builder_level')->nullable();
            $table->integer('baku_interactions')->nullable();
            $table->integer('community_activities')->nullable();
            $table->integer('voice_communications')->nullable();
            $table->integer('community_sentiment')->nullable();
            $table->integer('ranking_growth_rate')->nullable();
            $table->integer('baku_score')->nullable();
            $table->integer('baku_index')->nullable();
            $table->text('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('metrics');
    }
};
