@extends('layouts.activity')

@section('tab_content')
    <div class="table-responsive">
        <table class="table table-borderless">
            <thead class="text-center">
            <tr>
                <th>Rank</th>
                <th>User</th>
                <th>Total Points</th>
                <th>Completed Tasks</th>
                <th class="text-end">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($ranked_users as $index => $ranked_user)
                @php
                    $user = $ranked_user['user'];
                    $total_points = $ranked_user['total_points'];
                    
                    // 计算用户完成的任务数
                    $completed_tasks_count = \App\Models\UserTask::where('telegram_user_id', $user->id ?? null)
                        ->where('task_status', 'completed')
                        ->count();
                @endphp
                <tr>
                    <td class="text-center">
                        <span class="badge bg-primary">#{{ $index + $paginator->firstItem() }}</span>
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            <img class="rounded-circle" width="30" height="30"
                                 src="{{ $user ? ($user->getMeta('photo') ?? asset('images/baku/avatar.png')) : asset('images/baku/avatar.png') }}"
                                 onerror="this.src='{{asset('images/baku/avatar.png')}}'"
                                 alt="">
                            <div class="d-flex flex-column">
                                <div style="font-size: 14px; font-weight: 500;">
                                    {{ $user ? ($user->first_name ?? $user->username ?? 'Unknown User') : 'Unknown User' }}
                                </div>
                                <div style="font-size: 11px; color: #888888;">
                                    {{ $user ? ('@'.$user->username ?? '') : '' }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">{{ number_format($total_points) }}</td>
                    <td class="text-center">{{ $completed_tasks_count }}</td>
                    <td class="text-end">
                        <a href="#" class="btn btn-sm btn-outline-primary">View Profile</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No users have earned points yet</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $paginator->links() }}
    </div>
@endsection
