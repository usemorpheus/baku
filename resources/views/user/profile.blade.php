@extends('layouts.activity')

@section('tab_content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <div class="d-flex align-items-center">
                            <img class="rounded-circle me-3" width="40" height="40"
                                 src="{{ $telegramUser->getMeta('photo') ?? asset('images/baku/avatar.png') }}"
                                 onerror="this.src='{{asset('images/baku/avatar.png')}}'"
                                 alt="">
                            <div>
                                {{ $telegramUser->first_name ?? $telegramUser->username ?? 'Unknown User' }}
                                <small class="d-block text-muted">@{{ $telegramUser->username ?? 'unknown' }}</small>
                            </div>
                        </div>
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row text-center mb-4">
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="text-primary">{{ number_format($totalPoints) }}</h5>
                                    <p class="text-muted mb-0">Total Points</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="text-success">{{ $completedTasksCount }}</h5>
                                    <p class="text-muted mb-0">Completed Tasks</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="text-warning">{{ $revokedTasksCount }}</h5>
                                    <p class="text-muted mb-0">Revoked Tasks</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <h5>Recent Activity</h5>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Task</th>
                                        <th>Status</th>
                                        <th>Points</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($telegramUser->userTasks()->with('taskType')->latest()->take(10)->get() as $userTask)
                                    <tr>
                                        <td>{{ $userTask->taskType->title ?? 'Unknown Task' }}</td>
                                        <td>
                                            <span class="badge 
                                                @if($userTask->task_status === 'completed') bg-success 
                                                @elseif($userTask->task_status === 'pending') bg-warning 
                                                @elseif($userTask->task_status === 'revoked') bg-danger 
                                                @else bg-secondary @endif">
                                                {{ ucfirst($userTask->task_status) }}
                                            </span>
                                        </td>
                                        <td>{{ $userTask->effective_points }}</td>
                                        <td>{{ $userTask->created_at->format('M j, Y') }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No recent activity</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <a href="{{ route('tasks.index') }}" class="btn btn-outline-primary">
                            ← Back to Tasks
                        </a>
                        
                        @if($telegramUser->username)
                        <a href="https://t.me/{{ $telegramUser->username }}" target="_blank" class="btn btn-primary ms-2">
                            Contact on Telegram
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection