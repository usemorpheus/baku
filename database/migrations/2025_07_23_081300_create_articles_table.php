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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->nullable();
            $table->string('title')->nullable();
            $table->string('slug')->nullable();
            $table->string('category')->nullable();
            $table->boolean('published')->nullable();
            $table->string('image')->nullable();
            $table->string('author')->nullable();
            $table->text('content')->nullable();
            $table->text('data')->nullable();
            $table->integer('read_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
