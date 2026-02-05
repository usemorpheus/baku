<?php
// check_and_fix_db.php

// 检查数据库结构并修复缺失的字段
echo "Checking database structure for revoked fields...\n";

// 检查是否能连接到数据库并执行SQL
$connection_ok = true;
$pdo = null;

try {
    // 尝试使用PDO连接数据库
    $db_host = getenv('DB_HOST') ?: '127.0.0.1';
    $db_port = getenv('DB_PORT') ?: '5433';
    $db_name = getenv('DB_DATABASE') ?: 'baku';
    $db_user = getenv('DB_USERNAME') ?: 'forge';
    $db_pass = getenv('DB_PASSWORD') ?: 'localhost';
    
    $dsn = "pgsql:host=$db_host;port=$db_port;dbname=$db_name;";
    $pdo = new PDO($dsn, $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to database successfully.\n";
    
    // 检查user_tasks表结构
    $stmt = $pdo->query("SELECT column_name, data_type 
                         FROM information_schema.columns 
                         WHERE table_name = 'user_tasks'");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $columnNames = array_column($columns, 'column_name');
    echo "Current columns in user_tasks table:\n";
    foreach ($columnNames as $col) {
        echo "  - $col\n";
    }
    
    // 检查是否缺少revoked相关字段
    $missingFields = [];
    if (!in_array('revoked_at', $columnNames)) {
        $missingFields[] = 'revoked_at';
    }
    if (!in_array('revoked_reason', $columnNames)) {
        $missingFields[] = 'revoked_reason';
    }
    
    if (empty($missingFields)) {
        echo "All required fields exist in the database.\n";
    } else {
        echo "Missing fields: " . implode(', ', $missingFields) . "\n";
        
        // 尝试添加缺失的字段
        foreach ($missingFields as $field) {
            try {
                if ($field === 'revoked_at') {
                    $pdo->exec("ALTER TABLE user_tasks ADD COLUMN revoked_at TIMESTAMP(0) WITHOUT TIME ZONE NULL");
                    echo "Added field: $field\n";
                } elseif ($field === 'revoked_reason') {
                    $pdo->exec("ALTER TABLE user_tasks ADD COLUMN revoked_reason TEXT NULL");
                    echo "Added field: $field\n";
                }
            } catch (Exception $e) {
                echo "Error adding field $field: " . $e->getMessage() . "\n";
            }
        }
    }
    
    // 再次检查表结构
    $stmt = $pdo->query("SELECT column_name, data_type 
                         FROM information_schema.columns 
                         WHERE table_name = 'user_tasks' 
                         AND column_name IN ('revoked_at', 'revoked_reason')");
    $revokedColumns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($revokedColumns) > 0) {
        echo "\nRevoked fields are now available:\n";
        foreach ($revokedColumns as $col) {
            echo "  - {$col['column_name']} ({$col['data_type']})\n";
        }
    } else {
        echo "\nWarning: Revoked fields are still not available.\n";
    }
    
    // 检查是否有已撤销的任务
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM user_tasks WHERE task_status = 'revoked'");
    $revokedCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "\nTasks with status 'revoked': $revokedCount\n";
    
    // 检查add_bot_to_group任务
    $stmt = $pdo->query("SELECT ut.id, ut.task_status, ut.points, ut.revoked_at, tt.name 
                         FROM user_tasks ut 
                         JOIN task_types tt ON ut.task_type_id = tt.id 
                         WHERE tt.name = 'add_bot_to_group' 
                         ORDER BY ut.created_at DESC LIMIT 10");
    $botTasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\nRecent 'add_bot_to_group' tasks:\n";
    foreach ($botTasks as $task) {
        echo "  - ID: {$task['id']}, Status: {$task['task_status']}, Points: {$task['points']}, Revoked At: {$task['revoked_at']}\n";
    }
    
} catch (Exception $e) {
    echo "Database connection error: " . $e->getMessage() . "\n";
    $connection_ok = false;
}

if (!$connection_ok) {
    echo "Could not connect to database. Please ensure PostgreSQL is running and credentials are correct.\n";
}
?>