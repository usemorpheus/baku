@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Pending manual verification</h3>
        <p class="text-muted small mb-0">Follow Twitter & Retweet tasks that need approval or rejection.</p>
    </div>
    <div class="card-body p-0">
        @if($pendingTasks->isEmpty())
            <div class="p-4 text-center text-muted">No pending verifications.</div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Task</th>
                            <th>User</th>
                            <th>Submitted data</th>
                            <th>Submitted at</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingTasks as $task)
                            @php
                                $data = $task->task_data ?? [];
                                $user = $task->user;
                            @endphp
                            <tr>
                                <td>{{ $task->id }}</td>
                                <td>
                                    <strong>{{ $task->taskType->title ?? '-' }}</strong>
                                    <br><span class="badge bg-secondary">{{ $task->taskType->name ?? '' }}</span>
                                </td>
                                <td>
                                    @if($user)
                                        {{ $user->first_name ?? $user->username ?? 'N/A' }}
                                        @if($user->username)<br><small>{{ '@' . $user->username }}</small>@endif
                                        <br><small class="text-muted">{{ $task->telegram_user_id }}</small>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if(($task->taskType->name ?? '') === 'follow_twitter')
                                        <strong>Twitter:</strong> {{ $data['twitter_username'] ?? '—' }}
                                    @elseif(($task->taskType->name ?? '') === 'retweet_post')
                                        <strong>Twitter:</strong> {{ $data['twitter_username'] ?? '—' }}<br>
                                        <strong>Tweet URL:</strong>
                                        @if(!empty($data['tweet_url']))
                                            <a href="{{ $data['tweet_url'] }}" target="_blank" rel="noopener">Link</a>
                                        @else
                                            —
                                        @endif
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    <small>{{ isset($data['submitted_at']) ? \Carbon\Carbon::parse($data['submitted_at'])->format('Y-m-d H:i') : '—' }}</small>
                                </td>
                                <td class="text-end">
                                    <form method="POST" action="{{ route('admin.pending-verifications.approve', $task->id) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                    </form>
                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $task->id }}">Reject</button>
                                    <div class="modal fade" id="rejectModal{{ $task->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form method="POST" action="{{ route('admin.pending-verifications.reject', $task->id) }}">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Reject verification</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <label class="form-label">Reason (shown to user)</label>
                                                        <textarea name="rejection_reason" class="form-control" rows="3" required placeholder="e.g. Twitter username does not match / Retweet is not of the pinned tweet."></textarea>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger">Reject</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
