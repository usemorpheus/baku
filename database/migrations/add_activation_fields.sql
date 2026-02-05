-- Add activation fields to telegram_users table
ALTER TABLE telegram_users ADD COLUMN IF NOT EXISTS is_activated BOOLEAN DEFAULT FALSE;
ALTER TABLE telegram_users ADD COLUMN IF NOT EXISTS activated_at TIMESTAMP NULL;