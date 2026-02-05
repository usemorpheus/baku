<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 检查迁移是否已在迁移表中标记为已运行
        $migrationExists = DB::table('migrations')
            ->where('migration', '2026_02_05_145100_create_task_types_table')
            ->exists();
        
        // 如果原始迁移被标记为已运行，但表可能未正确创建，我们需要处理这种情况
        if ($migrationExists) {
            // 从migrations表中移除旧记录，允许新迁移运行
            DB::table('migrations')
                ->where('migration', '2026_02_05_145100_create_task_types_table')
                ->delete();
        }
        
        // 检查是否已存在需要的表
        $taskTypesExists = DB::select("SELECT to_regclass('task_types')"); 
        $userTasksExists = DB::select("SELECT to_regclass('user_tasks')"); 

        // 检查是否需要为telegram_users添加唯一约束
        $constraintExists = DB::select("
            SELECT 1 
            FROM pg_constraint 
            WHERE conname = 'telegram_users_id_unique'
        ");
        
        if (empty($constraintExists)) {
            // 检查是否有重复的ID
            $duplicateCheck = DB::select("
                SELECT id, COUNT(*) 
                FROM telegram_users 
                GROUP BY id 
                HAVING COUNT(*) > 1
            ");
            
            if (!empty($duplicateCheck)) {
                // 删除重复项，保留最新的记录
                DB::statement("
                    DELETE FROM telegram_users 
                    WHERE ctid NOT IN (
                        SELECT MAX(ctid) 
                        FROM telegram_users 
                        GROUP BY id
                    )
                ");
            }
            
            // 添加唯一约束
            try {
                DB::statement('ALTER TABLE telegram_users ADD CONSTRAINT telegram_users_id_unique UNIQUE (id)');
            } catch (\Exception $e) {
                // 如果约束已存在，忽略
            }
        }
        
        // 创建task_types表（如果不存在）
        if ($taskTypesExists[0]->to_regclass === null) {
            DB::statement("
                CREATE TABLE task_types (
                    id SERIAL PRIMARY KEY,
                    name VARCHAR(255) NOT NULL UNIQUE,
                    title VARCHAR(255) NOT NULL,
                    description TEXT NULL,
                    points_reward INTEGER NOT NULL DEFAULT 0,
                    verification_method VARCHAR(255) NOT NULL DEFAULT 'manual',
                    requirements JSON NULL,
                    is_active BOOLEAN NOT NULL DEFAULT true,
                    created_at TIMESTAMP(0) WITHOUT TIME ZONE NULL,
                    updated_at TIMESTAMP(0) WITHOUT TIME ZONE NULL
                )
            ");
            
            // 插入默认任务类型
            DB::statement("
                INSERT INTO task_types (name, title, description, points_reward, verification_method, requirements, is_active, created_at, updated_at) VALUES 
                ('add_bot_to_group', 'Add Bot to Group', 'Add @baku_news_bot to your Telegram group', 100, 'auto', '{\"bot_added\": true}', true, NOW(), NOW()),
                ('follow_twitter', 'Follow Twitter', 'Follow Baku official Twitter account: @Baku_builders', 100, 'api_check', '{\"twitter_username\": \"Baku_builders\"}', true, NOW(), NOW()),
                ('join_telegram_channel', 'Join Telegram Channel', 'Join our official Telegram channel', 50, 'manual', '{\"channel_username\": \"baku_channel\"}', true, NOW(), NOW()),
                ('retweet_post', 'Retweet Post', 'Retweet a post from @Baku_builders', 75, 'manual', '{\"must_retweet_from\": \"Baku_builders\"}', true, NOW(), NOW())
            ");
        }
        
        // 创建user_tasks表（如果不存在）
        if ($userTasksExists[0]->to_regclass === null) {
            DB::statement("
                CREATE TABLE user_tasks (
                    id SERIAL PRIMARY KEY,
                    telegram_user_id VARCHAR(255) NOT NULL,
                    task_type_id BIGINT NOT NULL,
                    task_status VARCHAR(255) NOT NULL DEFAULT 'pending',
                    points INTEGER NOT NULL DEFAULT 0,
                    task_data JSON NULL,
                    completed_at TIMESTAMP(0) WITHOUT TIME ZONE NULL,
                    verified_at TIMESTAMP(0) WITHOUT TIME ZONE NULL,
                    created_at TIMESTAMP(0) WITHOUT TIME ZONE NULL,
                    updated_at TIMESTAMP(0) WITHOUT TIME ZONE NULL
                )
            ");
            
            // 添加索引
            DB::statement('CREATE INDEX idx_user_tasks_user_id ON user_tasks(telegram_user_id)');
            DB::statement('CREATE INDEX idx_user_tasks_type_id ON user_tasks(task_type_id)');
            DB::statement('CREATE INDEX idx_user_tasks_status ON user_tasks(task_status)');
        } else {
            // 如果user_tasks表存在但缺少task_type_id列，添加它
            $columns = DB::select("
                SELECT column_name 
                FROM information_schema.columns 
                WHERE table_name = 'user_tasks' AND column_name = 'task_type_id'
            ");
            
            if (empty($columns)) {
                DB::statement('ALTER TABLE user_tasks ADD COLUMN task_type_id BIGINT');
                DB::statement('CREATE INDEX idx_user_tasks_type_id ON user_tasks(task_type_id)');
            }
        }
        
        // 最后，将这个迁移标记为已完成
        $migrationRecord = [
            'migration' => '2026_02_05_152000_complete_task_system_setup',
            'batch' => DB::table('migrations')->max('batch') + 1
        ];
        
        DB::table('migrations')->insert($migrationRecord);
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS user_tasks');
        DB::statement('DROP TABLE IF EXISTS task_types');
        
        // 移除唯一约束
        try {
            DB::statement('ALTER TABLE telegram_users DROP CONSTRAINT IF EXISTS telegram_users_id_unique');
        } catch (\Exception $e) {
            // 忽略错误
        }
        
        // 从migrations表中移除记录
        DB::table('migrations')
            ->where('migration', '2026_02_05_152000_complete_task_system_setup')
            ->delete();
    }
};