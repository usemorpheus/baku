# 数据库迁移说明

## 问题
需要向telegram_users表添加两个字段：
- is_activated: BOOLEAN DEFAULT FALSE
- activated_at: TIMESTAMP NULL

## 解决方案

### 方法1：使用Artisan Migrate (推荐)
```bash
cd /Users/babbage/Desktop/baku/code/baku
php artisan migrate
```

### 方法2：手动SQL命令
如果无法使用artisan，可以直接执行SQL：

```sql
-- PostgreSQL命令
ALTER TABLE telegram_users ADD COLUMN IF NOT EXISTS is_activated BOOLEAN DEFAULT FALSE;
ALTER TABLE telegram_users ADD COLUMN IF NOT EXISTS activated_at TIMESTAMP NULL;
```

### 方法3：使用psql客户端
```bash
psql -h [HOST] -U [USERNAME] -d [DATABASE] -c "ALTER TABLE telegram_users ADD COLUMN IF NOT EXISTS is_activated BOOLEAN DEFAULT FALSE;"
psql -h [HOST] -U [USERNAME] -d [DATABASE] -c "ALTER TABLE telegram_users ADD COLUMN IF NOT EXISTS activated_at TIMESTAMP NULL;"
```

## 验证
添加字段后，可以通过以下命令验证：
```sql
SELECT column_name, data_type 
FROM information_schema.columns 
WHERE table_name = 'telegram_users' 
AND column_name IN ('is_activated', 'activated_at');
```

## 注意事项
- 字段已添加后，激活识别功能将自动工作
- 用户在与Telegram机器人交互后，可以立即访问任务页面
- 无需重新部署或重启服务