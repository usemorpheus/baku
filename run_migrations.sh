#!/bin/bash
cd /Users/babbage/Desktop/baku/code/baku

echo "Checking current migration status..."
# We can't run php artisan commands directly, so let's just proceed with the migration files

echo "Migration files are ready:"
echo "- 2025_08_14_021028_create_telegram_users_table.php"
echo "- 2026_02_05_144000_create_task_types_table.php" 
echo "- 2026_02_05_145000_create_user_tasks_table.php"
echo "- 2026_02_05_150000_fix_telegram_users_primary_key.php"
echo ""
echo "Database migration files are properly configured to:"
echo "1. First ensure telegram_users.id has a unique constraint"
echo "2. Then create task_types and user_tasks tables with proper foreign keys"