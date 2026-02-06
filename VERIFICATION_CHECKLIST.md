## Baku积分增减逻辑验证清单

### 1. 添加机器人到群组 (+100积分)
- [x] Telegram webhook接收事件
- [x] 识别为baku_news_bot被添加到群组
- [x] 创建UserTask记录
- [x] 任务状态设为'completed'
- [x] 积分计算包含此任务
- [x] 用户积分增加

### 2. 从群组移除机器人 (-100积分)
- [x] Telegram webhook接收事件
- [x] 识别为baku_news_bot从群组移除
- [x] revokeAddBotToGroupTask()方法被调用
- [x] 正确匹配chat_id查找对应任务
- [x] 任务状态更新为'revoked'
- [x] effective_points访问器返回负值
- [x] 积分计算排除或减去此任务
- [x] 用户积分减少

### 3. 任务可重认领
- [x] revoked任务不再阻止用户重新认领
- [x] 任务重新出现在可用任务列表中
- [x] 用户可再次完成同一任务

### 4. 数据库兼容性
- [x] 即使缺少revoked_at和revoked_reason字段也能工作
- [x] 仅依赖task_status字段进行状态判断

### 5. 用户界面反馈
- [x] 显示已完成任务
- [x] 显示已撤销任务
- [x] 显示净积分（完成-撤销）
- [x] 提供清晰的操作反馈

所有逻辑均已验证并修复。