@extends('layouts.activity')

@section('tab_content')
    <div class="d-flex justify-content-end mb-3">
        <button type="button" class="btn btn-primary rounded-2" style="padding: 10px 25px" onclick="checkAndNavigateToTasks()">
            Complete Tasks & Earn Points
            <svg width="16" height="15" viewBox="0 0 16 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M10.3242 0.800781L9.42578 1.69922L13.3516 5.625H4.875C4.08073 5.625 3.34831 5.82031 2.67773 6.21094C2.00716 6.60156 1.47656 7.13216 1.08594 7.80273C0.695312 8.47331 0.5 9.20573 0.5 10C0.5 10.7943 0.695312 11.5267 1.08594 12.1973C1.47656 12.8678 2.00716 13.3984 2.67773 13.7891C3.34831 14.1797 4.08073 14.375 4.875 14.375V13.125C4.30208 13.125 3.77799 12.985 3.30273 12.7051C2.82747 12.4251 2.44987 12.0475 2.16992 11.5723C1.88997 11.097 1.75 10.5729 1.75 10C1.75 9.42708 1.88997 8.90299 2.16992 8.42773C2.44987 7.95247 2.82747 7.57487 3.30273 7.29492C3.77799 7.01497 4.30208 6.875 4.875 6.875H13.3516L9.42578 10.8008L10.3242 11.6992L15.3242 6.69922L15.7539 6.25L15.3242 5.80078L10.3242 0.800781Z" fill="#F6F4F3"/>
            </svg>
        </button>
    </div>
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
                        @if($user)
                            <a href="{{ route('tasks.user-profile', ['telegramUserId' => $user->id]) }}" class="btn btn-sm btn-outline-secondary" style="border-radius: 8px; padding: 4px 12px; font-size: 12px; border-color: #dee2e6;">
                                <i class="la la-user"></i> View Profile
                            </a>
                        @else
                            <span class="text-muted" style="font-size: 12px;">Profile unavailable</span>
                        @endif
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
