<?php
// direct_db_update.php - 直接更新数据库结构

// 加载Composer autoloader
require_once __DIR__.'/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

// 从.env文件读取数据库配置
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// 初始化Capsule
$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => $_ENV['DB_CONNECTION'] ?? 'pgsql',
    'host'      => $_ENV['DB_HOST'] ?? 'localhost',
    'port'      => $_ENV['DB_PORT'] ?? 5432,
    'database'  => $_ENV['DB_DATABASE'] ?? 'baku',
    'username'  => $_ENV['DB_USERNAME'] ?? 'postgres',
    'password'  => $_ENV['DB_PASSWORD'] ?? '',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

// 检查并添加字段
try {
    // 检查表是否存在
    $schema = $capsule::schema();
    
    if ($schema->hasTable('telegram_users')) {
        $columns = array_map(function($col) {
            return $col->name;
        }, $capsule::select('SELECT column_name as name FROM information_schema.columns WHERE table_name = \'telegram_users\''));
        
        $missing_columns = [];
        
        if (!in_array('is_activated', $columns)) {
            $missing_columns[] = 'is_activated';
        }
        
        if (!in_array('activated_at', $columns)) {
            $missing_columns[] = 'activated_at';
        }
        
        if (!empty($missing_columns)) {
            foreach ($missing_columns as $col) {
                if ($col === 'is_activated') {
                    $capsule::statement('ALTER TABLE telegram_users ADD COLUMN is_activated BOOLEAN DEFAULT FALSE;');
                } elseif ($col === 'activated_at') {
                    $capsule::statement('ALTER TABLE telegram_users ADD COLUMN activated_at TIMESTAMP NULL;');
                }
            }
            echo "字段添加成功: " . implode(', ', $missing_columns) . "\n";
        } else {
            echo "所有必需的字段已存在\n";
        }
    } else {
        echo "表 telegram_users 不存在\n";
    }
    
    echo "数据库更新完成\n";
    
} catch (Exception $e) {
    echo "错误: " . $e->getMessage() . "\n";
}