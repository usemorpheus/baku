-- 手动添加激活字段到telegram_users表
-- 如果使用PostgreSQL，运行以下命令：

-- 添加 is_activated 字段（如果不存在）
DO $$ 
BEGIN
    IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_name = 'telegram_users' AND column_name = 'is_activated') THEN
        ALTER TABLE telegram_users ADD COLUMN is_activated BOOLEAN DEFAULT FALSE;
    END IF;
END $$;

-- 添加 activated_at 字段（如果不存在）
DO $$ 
BEGIN
    IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_name = 'telegram_users' AND column_name = 'activated_at') THEN
        ALTER TABLE telegram_users ADD COLUMN activated_at TIMESTAMP NULL;
    END IF;
END $$;

-- 创建索引以提高查询性能
CREATE INDEX IF NOT EXISTS idx_telegram_users_activated ON telegram_users (is_activated, activated_at);