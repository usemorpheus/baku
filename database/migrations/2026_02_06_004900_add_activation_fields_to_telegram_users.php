<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 检查字段是否已存在，避免重复创建
        $columns = [];
        $tableExists = Schema::hasTable('telegram_users');
        
        if ($tableExists) {
            $columns = Schema::getColumnListing('telegram_users');
        }
        
        // 添加 is_activated 字段（如果不存在）
        if ($tableExists && !in_array('is_activated', $columns)) {
            Schema::table('telegram_users', function (Blueprint $table) {
                $table->boolean('is_activated')->default(false);
            });
        }
        
        // 添加 activated_at 字段（如果不存在）
        if ($tableExists && !in_array('activated_at', $columns)) {
            Schema::table('telegram_users', function (Blueprint $table) {
                $table->timestamp('activated_at')->nullable();
            });
        }
        
        // 如果表不存在，创建它（预防性措施）
        if (!$tableExists) {
            Schema::create('telegram_users', function (Blueprint $table) {
                $table->string('id');
                $table->boolean('is_bot')->default(false);
                $table->string('first_name')->nullable();
                $table->string('username')->nullable();
                $table->boolean('is_activated')->default(false);
                $table->timestamp('activated_at')->nullable();
                $table->timestamps();
                
                $table->primary('id');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('telegram_users')) {
            $columns = Schema::getColumnListing('telegram_users');
            
            if (in_array('is_activated', $columns)) {
                Schema::table('telegram_users', function (Blueprint $table) {
                    $table->dropColumn('is_activated');
                });
            }
            
            if (in_array('activated_at', $columns)) {
                Schema::table('telegram_users', function (Blueprint $table) {
                    $table->dropColumn('activated_at');
                });
            }
        }
    }
};