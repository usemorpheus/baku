# Baku Task System Migration Guide

This guide explains how to safely apply the task-driven积分 system to your Baku installation.

## Overview

The task-driven积分 system replaces the previous community activity-based system with a more flexible task-completion model. Users can now earn points by completing specific tasks like:
- Adding the @baku_news_bot to their Telegram group (+100 points)
- Following the official Twitter account @Baku_builders (+100 points)
- Joining the official Telegram channel (+50 points)
- Retweeting posts (+75 points)

## Migration Process

The migration has been designed to be safe and handle existing data appropriately:

1. **Unique Constraint on telegram_users**: Adds a unique constraint to the id field which is required for foreign key relationships
2. **Task Types Table**: Creates the task_types table with pre-defined tasks
3. **User Tasks Table**: Creates the user_tasks table to track task completions
4. **Data Integrity**: Handles duplicate records and ensures referential integrity

## To Apply Migration

Run the following command:

```bash
php artisan migrate
```

## Rollback

If you need to rollback:

```bash
php artisan migrate:rollback
```

## Post-Migration Steps

After successful migration, the system will:

1. Allow users to claim and complete tasks via the /tasks route
2. Automatically award points when tasks are completed and verified
3. Track user积分 in the user_tasks table
4. Display updated积分 rankings on the /activity/points page
5. Continue to support automatic积分 for adding bots to groups

## Verification

You can verify the migration worked by checking:

- The task_types table contains the four default tasks
- The user_tasks table exists and has the correct structure
- The telegram_users table has a unique constraint on the id field
- The积分 rankings page shows user-specific task-based积分