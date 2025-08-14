<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('twitter_posts', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('text')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

};
