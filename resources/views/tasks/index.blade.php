@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Baku Tasks</h2>
    <p>Hello, <strong>{{ $telegramUser->first_name ?? 'User' }}</strong>! Earn points by completing tasks.</p>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Available Tasks</h4>
                </div>
                <div class="card-body">
                    @forelse($availableTasks as $task)
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title">{{ $task->title }}</h5>
                                <p class="card-text">{{ $task->description }}</p>
                                <p class="text-success"><strong>{{ $task->points_reward }} Points</strong></p>
                                
                                @if(in_array($task->name, ['add_bot_to_group', 'follow_twitter', 'join_telegram_channel', 'retweet_post']))
                                    <div class="alert alert-info">
                                        <strong>Instructions:</strong> 
                                        @if($task->name === 'add_bot_to_group')
                                            Add <code>@baku_news_bot</code> to your Telegram group and send a message to verify.
                                        @elseif($task->name === 'follow_twitter')
                                            Follow <a href="https://x.com/Baku_builders" target="_blank">@Baku_builders</a> on Twitter.
                                        @elseif($task->name === 'join_telegram_channel')
                                            Join the official Telegram channel <a href="https://t.me/bakubuilders" target="_blank">@bakubuilders</a>.
                                        @elseif($task->name === 'retweet_post')
                                            Retweet the <strong>pinned tweet</strong> from <a href="https://x.com/Baku_builders" target="_blank">@Baku_builders</a> on Twitter/X, then submit the retweet link and your Twitter username.
                                        @endif
                                    </div>
                                @endif
                                
                                <form method="POST" action="{{ route('tasks.claim', $task->id) }}" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">
                                        Claim Task
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p>No tasks available at the moment.</p>
                    @endforelse
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4>Your Progress</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h5>Total Points</h5>
                        <p class="display-4 text-primary">
                            {{ max(0, $completedTasks->sum('points') - $revokedTasks->sum('points')) }}
                        </p>
                    </div>
                    
                    <div class="mb-3">
                        <h6>Completed Tasks</h6>
                        <ul class="list-group">
                            @forelse($completedTasks as $task)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>{{ $task->taskType->title }}</span>
                                    <span class="badge bg-success">{{ $task->points }} pts</span>
                                </li>
                            @empty
                                <li class="list-group-item">No completed tasks yet</li>
                            @endforelse
                        </ul>
                    </div>
                    
                    <div class="mb-3">
                        <h6>Pending Tasks</h6>
                        <ul class="list-group">
                            @forelse($pendingTasks as $task)
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                        <span>{{ $task->taskType->title }}</span>
                                        <span class="badge bg-warning">{{ ucfirst(str_replace('_', ' ', $task->task_status)) }}</span>
                                    </div>
                                    @if($task->taskType->name === 'join_telegram_channel')
                                        <p class="small text-muted mb-2 mt-2">Join <a href="https://t.me/bakubuilders" target="_blank">@bakubuilders</a> then click Verify.</p>
                                        <form method="POST" action="{{ route('tasks.verify', $task->id) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-primary">Verify</button>
                                        </form>
                                    @elseif($task->taskType->name === 'retweet_post')
                                        <p class="small text-muted mb-2 mt-2">Submit your retweet link and Twitter username (must be the pinned tweet from @Baku_builders).</p>
                                        <form method="POST" action="{{ route('tasks.verify', $task->id) }}" class="mt-2">
                                            @csrf
                                            <input type="url" name="tweet_url" class="form-control form-control-sm mb-1" placeholder="Retweet URL" required>
                                            <input type="text" name="twitter_username" class="form-control form-control-sm mb-1" placeholder="Your Twitter username" value="{{ $task->task_data['twitter_username'] ?? '' }}" required>
                                            <button type="submit" class="btn btn-sm btn-outline-primary">Submit verification</button>
                                        </form>
                                    @elseif($task->taskType->name === 'follow_twitter')
                                        <form method="POST" action="{{ route('tasks.verify', $task->id) }}" class="d-inline mt-2">
                                            @csrf
                                            <input type="text" name="twitter_username" class="form-control form-control-sm d-inline-block mb-1" style="width: 140px;" placeholder="Your Twitter username" value="{{ $task->task_data['twitter_username'] ?? '' }}">
                                            <button type="submit" class="btn btn-sm btn-outline-primary">Submit verification</button>
                                        </form>
                                    @endif
                                </li>
                            @empty
                                <li class="list-group-item">No pending tasks</li>
                            @endforelse
                        </ul>
                    </div>
                    
                    <div>
                        <h6>Revoked Tasks</h6>
                        <ul class="list-group">
                            @forelse($revokedTasks as $task)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>{{ $task->taskType->title }} <small class="text-muted">(Revoked)</small></span>
                                    <span class="badge bg-danger">-{{ $task->points }} pts</span>
                                </li>
                            @empty
                                <li class="list-group-item">No revoked tasks</li>
                            @endforelse
                        </ul>
                    </div>
                    
                    <!-- 添加说明 -->
                    <div class="alert alert-info mt-3">
                        <strong>Note:</strong> Revoked tasks have had their points deducted. You can reclaim revoked tasks by completing them again.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection