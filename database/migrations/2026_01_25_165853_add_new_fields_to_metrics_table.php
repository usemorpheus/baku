<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('metrics', function (Blueprint $table) {
            // 合约地址字段
            $table->string('contract_address', 70)
                ->nullable()
                ->after('telegram_chat_id') // 放在 telegram_chat_id 字段后面
                ->comment('合约地址，例如: 0x1a1e69f1e6182e2f8b9e8987e83c016ac9444444');

            // 上次价格字段（最多18位小数）
            $table->decimal('last_price', 36, 18)
                ->nullable()
                ->default(0)
                ->after('contract_address')
                ->comment('资产上一次的价格，最多18位小数');

            // 当前价格字段（最多18位小数）
            $table->decimal('price', 36, 18)
                ->nullable()
                ->default(0)
                ->after('last_price')
                ->comment('资产当前价格，最多18位小数');

            // 上次综合排名字段
            $table->integer('last_baku_index')
                ->nullable()
                ->after('price')
                ->comment('上一次综合排名');

            // 可选：为合约地址添加索引
            $table->index('contract_address', 'idx_contract_address');

            // 可选：为价格字段添加索引（如果需要范围查询）
            $table->index('price', 'idx_price');

            // 可选：为 last_baku_index 添加索引
            $table->index('last_baku_index', 'idx_last_baku_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('metrics', function (Blueprint $table) {
            // 删除索引（如果有）
            $table->dropIndex('idx_contract_address');
            $table->dropIndex('idx_price');
            $table->dropIndex('idx_last_baku_index');

            // 删除字段
            $table->dropColumn([
                'contract_address',
                'last_price',
                'price',
                'last_baku_index'
            ]);
        });
    }
};
