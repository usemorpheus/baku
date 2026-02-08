<?php

namespace App\Http\Controllers\Admin;

use App\Models\UserTask;
use App\Models\TaskType;
use Illuminate\Http\Request;

class PendingVerificationController
{
    public function index()
    {
        $taskTypeIds = TaskType::whereIn('name', ['follow_twitter', 'retweet_post'])->pluck('id');

        $pendingTasks = UserTask::with(['user', 'taskType'])
            ->where('task_status', 'pending')
            ->whereIn('task_type_id', $taskTypeIds)
            ->orderBy('updated_at', 'desc')
            ->get()
            ->filter(function (UserTask $task) {
                $data = $task->task_data ?? [];
                return !empty($data['under_review']);
            });

        admin()->pageTitle('Manual verification')
            ->title('Manual verification')
            ->content(view('admin.pending-verifications.index', [
                'pendingTasks' => $pendingTasks,
            ])->render());

        return admin()->render();
    }

    public function approve(int $id)
    {
        $userTask = UserTask::findOrFail($id);
        $taskName = $userTask->taskType->name ?? '';
        if (!in_array($taskName, ['follow_twitter', 'retweet_post'], true)) {
            return redirect()->route('admin.pending-verifications.index')
                ->with('error', 'Task type cannot be approved from this page.');
        }

        $taskData = $userTask->task_data ?? [];
        unset($taskData['under_review'], $taskData['rejection_reason'], $taskData['submitted_at']);

        $userTask->update([
            'task_status' => 'completed',
            'verified_at' => now(),
            'completed_at' => now(),
            'task_data' => $taskData,
        ]);

        return redirect()->route('admin.pending-verifications.index')
            ->with('success', 'Task approved and points granted.');
    }

    public function reject(Request $request, int $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $userTask = UserTask::findOrFail($id);
        $taskName = $userTask->taskType->name ?? '';
        if (!in_array($taskName, ['follow_twitter', 'retweet_post'], true)) {
            return redirect()->route('admin.pending-verifications.index')
                ->with('error', 'Task type cannot be rejected from this page.');
        }

        $taskData = $userTask->task_data ?? [];
        $taskData['rejection_reason'] = $request->input('rejection_reason');
        $taskData['under_review'] = false;

        $userTask->update([
            'task_data' => $taskData,
        ]);

        return redirect()->route('admin.pending-verifications.index')
            ->with('success', 'Task rejected. User can resubmit with new information.');
    }
}
