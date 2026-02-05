-- 首先检查现有表结构
SELECT table_name, column_name, data_type 
FROM information_schema.columns 
WHERE table_name IN ('task_types', 'user_tasks', 'telegram_users')
ORDER BY table_name, ordinal_position;