<?php
// add_revocation_fields.php

// 设置错误报告
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 加载composer autoloader
require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

// 从.env文件读取配置
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// 初始化Laravel的数据库组件
$capsule = new Capsule;

$capsule->addConnection([
    'driver' => 'pgsql',
    'host' => $_ENV['DB_HOST'] ?? '127.0.0.1',
    'port' => $_ENV['DB_PORT'] ?? 5432,
    'database' => $_ENV['DB_DATABASE'] ?? 'baku',
    'username' => $_ENV['DB_USERNAME'] ?? 'forge',
    'password' => $_ENV['DB_PASSWORD'] ?? 'localhost',
    'charset' => 'utf8',
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

try {
    // 检查user_tasks表中是否已存在revoked_at和revoked_reason字段
    $columns = Capsule::select("
        SELECT column_name 
        FROM information_schema.columns 
        WHERE table_name = 'user_tasks' 
        AND column_name IN ('revoked_at', 'revoked_reason')
    ");
    
    $existingColumns = array_column($columns, 'column_name');
    
    echo "Existing columns in user_tasks: " . implode(', ', $existingColumns) . "\n";
    
    // 添加缺失的字段
    if (!in_array('revoked_at', $existingColumns)) {
        echo "Adding revoked_at column...\n";
        Capsule::statement("ALTER TABLE user_tasks ADD COLUMN revoked_at TIMESTAMP(0) WITHOUT TIME ZONE NULL");
        echo "revoked_at column added.\n";
    } else {
        echo "revoked_at column already exists.\n";
    }
    
    if (!in_array('revoked_reason', $existingColumns)) {
        echo "Adding revoked_reason column...\n";
        Capsule::statement("ALTER TABLE user_tasks ADD COLUMN revoked_reason TEXT NULL");
        echo "revoked_reason column added.\n";
    } else {
        echo "revoked_reason column already exists.\n";
    }
    
    echo "Migration completed successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}