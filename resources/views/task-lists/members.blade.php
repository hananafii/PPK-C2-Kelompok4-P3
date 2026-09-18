@extends('layouts.app')

@section('title', 'Collaboration - ' . $taskList->name)
@section('page-title', 'Team Collaboration')

@section('content')
<!-- Back Navigation & Breadcrumb -->
<div class="mb-3">
    <a href="{{ route('task-lists.show', $taskList) }}" class="btn btn-sm btn-light border text-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to {{ $taskList->name }}
    </a>
</div>

<div class="row g-4">
    <!-- Left Column: Invite Member Card & Task List Progress -->
    <div class="col-lg-4">
        <!-- Task List Info & Progress Card -->
        <div class="card shadow-sm border-0 mb-4 p-4">
            <h5 class="fw-bold text-dark mb-1">{{ $taskList->name }}</h5>
            <p class="text-muted small mb-3">
                {{ $taskList->description ?? 'No description provided.' }}
            </p>

            <div class="p-3 bg-light rounded-3 border mb-3">
                <div class="d-flex justify-content-between small mb-1">
                    <span class="text-muted fw-semibold">Task Completion</span>
                    <span class="fw-bold text-primary">{{ $progress }}%</span>
                </div>
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $progress }}%;"></div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 small text-muted">
                <i class="bi bi-person-badge-fill text-primary"></i>
                <span>Owner: <strong>{{ $taskList->owner->name }}</strong></span>
            </div>
        </div>

        <!-- Invite Member Section -->
        <div class="card shadow-sm border-0 p-4">
            <h6 class="fw-bold text-dark mb-2">
                <i class="bi bi-person-plus-fill text-primary me-1"></i> Invite New Member
            </h6>
            <p class="text-muted small mb-3">
                Grant team members access to view, update, and manage tasks in this list.
            </p>

            @if($availableUsers->isEmpty())
                <div class="alert alert-info small mb-0 rounded-3">
                    <i class="bi bi-info-circle me-1"></i> All registered users are already members of this list.
                </div>
            @else
                <form method="POST" action="{{ route('task-lists.members.store', $taskList) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary" for="user_id">Select User</label>
                        <select name="user_id" id="user_id" class="form-select" required>
                            <option value="">-- Choose User --</option>
                            @foreach($availableUsers as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-jara-primary w-100">
                        <i class="bi bi-send-fill me-1"></i> Add to Team
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Right Column: Current Members List -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="fw-bold text-dark mb-0">List Collaborators</h5>
                    <small class="text-muted">People who have shared access to this task list</small>
                </div>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1">
                    {{ $taskList->members->count() + 1 }} People
                </span>
            </div>

            <!-- Owner Card -->
            <div class="card border mb-3 p-3 bg-light-subtle">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar-circle bg-primary text-white" style="width: 42px; height: 42px; font-size: 1rem; border: 2px solid #4338ca;">
                            {{ strtoupper(substr($taskList->owner->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="fw-bold text-dark">{{ $taskList->owner->name }}</div>
                            <div class="small text-muted">{{ $taskList->owner->email }}</div>
                        </div>
                    </div>
                    <span class="badge bg-primary rounded-pill px-3 py-1">
                        <i class="bi bi-shield-check me-1"></i> Owner
                    </span>
                </div>
            </div>

            <!-- Invited Members List -->
            @if($taskList->members->isEmpty())
                <div class="empty-state-box my-3 py-4">
                    <div class="empty-state-icon" style="width: 48px; height: 48px; font-size: 1.5rem;">
                        <i class="bi bi-people"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">No collaborators yet</h6>
                    <p class="text-muted small mb-0">Use the form on the left to invite teammates to this list.</p>
                </div>
            @else
                <div class="d-flex flex-column gap-2">
                    @foreach($taskList->members as $member)
                        <div class="card border p-3 card-hover">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-circle" style="width: 40px; height: 40px; font-size: 0.95rem;">
                                        {{ strtoupper(substr($member->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $member->name }}</div>
                                        <div class="small text-muted">{{ $member->email }}</div>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill px-2 py-1 small">
                                        Collaborator
                                    </span>

                                    <form method="POST" action="{{ route('task-lists.members.destroy', [$taskList, $member]) }}" onsubmit="return confirm('Are you sure you want to remove {{ addslashes($member->name) }} from this list?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Remove Member">
                                            <i class="bi bi-person-x"></i> Remove
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
