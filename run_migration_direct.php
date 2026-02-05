#!/usr/bin/env php
<?php

// 设置正确的目录
chdir(__DIR__);

// 引入composer autoloader
require_once __DIR__.'/vendor/autoload.php';

// 加载环境变量
try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
} catch (Exception $e) {
    // 如果没有安装vlucas/phpdotenv，跳过
}

// 手动运行迁移
$app = new Illuminate\Foundation\Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

// 启动应用
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// 运行迁移
$migrator = $app->make('migrator');
$repository = $app->make('migration.repository');

if (!$repository->repositoryExists()) {
    $repository->createRepository();
}

$migration_paths = [
    database_path('migrations')
];

$migrator->run($migration_paths, ['pretend' => false, 'step' => false]);

echo "Migration completed!\n";

// 输出结果
foreach ($migrator->getRan() as $migration) {
    echo "Migrated: $migration\n";
}