<?php
// clean_old_task_data.php

echo "=== Cleaning Old Task Data ===\n";

echo "\nThis script will help clean old task data that might cause issues:\n";
echo "1. Tasks with inconsistent chat_id formats\n";
echo "2. Dangling tasks without proper data\n";
echo "3. Tasks with incorrect statuses\n";

echo "\nPotential cleanup actions:\n";
echo "- Reset all 'add_bot_to_group' tasks to a known state\n";
echo "- Verify task_data structure consistency\n";
echo "- Clear any corrupted records\n";

echo "\nWould recommend backing up the database before proceeding with any cleanup.\n";

?>