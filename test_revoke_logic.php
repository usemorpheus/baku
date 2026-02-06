<?php
// test_revoke_logic.php

echo "Testing the revoke logic:\n";

echo "\n1. When bot is ADDED to group:\n";
echo "   - handleAddBotToGroupTask() is called\n";
echo "   - Creates UserTask record with:\n";
echo "     * task_status = 'completed'\n";
echo "     * points = 100\n";
echo "     * task_data = {'chat_id': -5145003749, 'chat_title': 'group_name', 'added_at': 'timestamp'}\n";

echo "\n2. When bot is REMOVED from group:\n";
echo "   - handleTaskUpdates() receives 'left_chat_member' event\n";
echo "   - Calls revokeAddBotToGroupTask(chatId, removerId)\n";
echo "   - Finds UserTask with matching chat_id in task_data\n";
echo "   - Updates task_status to 'revoked'\n";

echo "\n3. Points calculation:\n";
echo "   - Before: User has +100 points from completed task\n";
echo "   - After revoke: Task status becomes 'revoked'\n";
echo "   - Points calculation now considers revoked tasks as negative\n";

echo "\n4. Task availability:\n";
echo "   - When task is revoked, it should become available for re-claiming\n";
echo "   - This allows user to add bot again and earn points again\n";

echo "\n5. Verification:\n";
echo "   - Check if task_data.chat_id matches the removed chat ID\n";
echo "   - Update task_status to 'revoked'\n";
echo "   - Points calculation should exclude revoked tasks\n";

?>