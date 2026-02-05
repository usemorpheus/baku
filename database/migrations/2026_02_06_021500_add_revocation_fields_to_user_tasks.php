<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 检查表是否存在
        $userTasksExists = DB::select("SELECT to_regclass('user_tasks')"); 
        
        if ($userTasksExists[0]->to_regclass !== null) {
            // 检查是否已存在revoked相关字段
            $revokedAtColumn = DB::select("
                SELECT column_name 
                FROM information_schema.columns 
                WHERE table_name = 'user_tasks' AND column_name = 'revoked_at'
            ");
            
            if (empty($revokedAtColumn)) {
                // 添加撤销相关的字段
                DB::statement('ALTER TABLE user_tasks ADD COLUMN revoked_at TIMESTAMP(0) WITHOUT TIME ZONE NULL');
                DB::statement('ALTER TABLE user_tasks ADD COLUMN revoked_reason TEXT NULL');
                
                // 确保task_status列允许'revoked'值
                // 对于PostgreSQL，我们不需要特别处理，因为VARCHAR可以存储任何字符串值
            }
        }
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE user_tasks DROP COLUMN IF EXISTS revoked_at');
        DB::statement('ALTER TABLE user_tasks DROP COLUMN IF EXISTS revoked_reason');
    }
};