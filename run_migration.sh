#!/bin/bash

# 尝试找到PHP的安装位置
PHP_PATH=$(brew --prefix)/bin/php

if [ -f "$PHP_PATH" ]; then
    echo "Found PHP at: $PHP_PATH"
    cd /Users/babbage/Desktop/baku/code/baku
    $PHP_PATH artisan migrate
else
    echo "Could not find PHP at expected location"
    echo "Trying common locations..."
    
    # 尝试常见位置
    COMMON_PHP_PATHS=(
        "/usr/local/bin/php"
        "/opt/homebrew/bin/php"
        "/usr/bin/php"
        "/Applications/MAMP/bin/php/php*/bin/php"
    )
    
    for path in "${COMMON_PHP_PATHS[@]}"; do
        if [ -f "$path" ]; then
            echo "Found PHP at: $path"
            cd /Users/babbage/Desktop/baku/code/baku
            $path artisan migrate
            exit 0
        fi
    done
    
    echo "Could not find PHP in common locations"
    echo "Running direct database update script instead..."
    php /Users/babbage/Desktop/baku/code/baku/direct_db_update.php
fi