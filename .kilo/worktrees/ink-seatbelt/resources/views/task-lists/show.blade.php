@extends('layouts.app')

@section('title', $taskList->name)
@section('page-title', 'Task List: ' . $taskList->name)

@section('content')
<div class="row g-4 mb-4">
    <!-- List Summary Card -->
    <div class="col-lg-8">
        <div class="card p-4 shadow-sm h-100">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <h4 class="fw-bold text-dark mb-1">{{ $taskList->name }}</h4>
                    <p class="text-muted mb-3">{{ $taskList->description ?? 'No description provided.' }}</p>
                </div>

                @if(Auth::user()->isAdmin() || $taskList->isOwnedBy(Auth::user()))
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light border p-1 px-2" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-gear-fill text-secondary"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                            <li><a class="dropdown-item small" href="{{ route('task-lists.edit', $taskList) }}"><i class="bi bi-pencil me-2"></i> Edit List Info</a></li>
                            <li>
                                <form action="{{ route('task-lists.destroy', $taskList) }}" method="POST" onsubmit="return confirm('Delete this list and all tasks?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="dropdown-item small text-danger"><i class="bi bi-trash me-2"></i> Delete List</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @endif
            </div>

            <!-- Stats badges -->
            <div class="d-flex flex-wrap gap-2 mb-3">
                <span class="badge bg-light text-secondary border px-3 py-2">
                    <i class="bi bi-check2-square me-1"></i> {{ $totalCount }} Total Tasks
                </span>
                <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-3 py-2">
                    <i class="bi bi-check-circle-fill me-1"></i> {{ $completedCount }} Completed
                </span>
                <span class="badge bg-info bg-opacity-10 text-info border border-info-subtle px-3 py-2">
                    <i class="bi bi-arrow-repeat me-1"></i> {{ $inProgressCount }} In Progress
                </span>
                <span class="badge bg-secondary bg-opacity-10 text-secondary border px-3 py-2">
                    <i class="bi bi-hourglass-split me-1"></i> {{ $pendingCount }} Pending
                </span>
            </div>

            <!-- Progress Bar -->
            <div>
                <div class="d-flex justify-content-between small text-muted mb-1">
                    <span>Task Completion Progress</span>
                    <span class="fw-bold">{{ $progress }}%</span>
                </div>
                <div class="progress" style="height: 10px;">
                    <div class="progress-bar progress-bar-jara progress-bar-striped progress-bar-animated" role="progressbar" style="width: {{ $progress }}%;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Team Collaboration Card -->
    <div class="col-lg-4">
        <div class="card p-4 shadow-sm h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-people-fill text-primary"></i> Team Members
                </h6>

                <div class="d-flex align-items-center gap-1">
                    <a href="{{ route('task-lists.members', $taskList) }}" class="btn btn-sm btn-light border py-1 px-2" title="Open Dedicated Member Management Page">
                        <i class="bi bi-gear me-1"></i> Manage
                    </a>
                    @if(Auth::user()->isAdmin() || $taskList->isOwnedBy(Auth::user()))
                        <button class="btn btn-sm btn-outline-primary py-1 px-2" data-bs-toggle="modal" data-bs-target="#inviteMemberModal">
                            <i class="bi bi-person-plus-fill me-1"></i> Invite
                        </button>
                    @endif
                </div>
            </div>

            <!-- List of members -->
            <div class="d-flex flex-column gap-2 overflow-auto" style="max-height: 200px;">
                <!-- Owner -->
                <div class="d-flex justify-content-between align-items-center p-2 rounded-3 bg-light border">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar-circle bg-primary text-white" style="width: 32px; height: 32px; font-size: 0.8rem;">
                            {{ strtoupper(substr($taskList->owner->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="fw-bold small mb-0">{{ $taskList->owner->name }}</div>
                            <small class="text-muted" style="font-size: 0.7rem;">{{ $taskList->owner->email }}</small>
                        </div>
                    </div>
                    <span class="badge bg-primary text-white" style="font-size: 0.65rem;">Owner</span>
                </div>

                <!-- Collaborators -->
                @forelse($taskList->members as $member)
                    <div class="d-flex justify-content-between align-items-center p-2 rounded-3 bg-light border">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-circle" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                {{ strtoupper(substr($member->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-semibold small mb-0">{{ $member->name }}</div>
                                <small class="text-muted" style="font-size: 0.7rem;">{{ $member->email }}</small>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-1">
                            <span class="badge bg-info bg-opacity-25 text-info-emphasis border" style="font-size: 0.65rem;">Member</span>
                            @if(Auth::user()->isAdmin() || $taskList->isOwnedBy(Auth::user()))
                                <form action="{{ route('task-lists.members.remove', [$taskList, $member]) }}" method="POST" onsubmit="return confirm('Remove {{ $member->name }} from this task list?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm text-danger p-0 px-1" title="Remove member">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-3 text-muted small">
                        <em>No collaborators invited yet.</em>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Tasks Section -->
<div class="card shadow-sm">
    <div class="card-header bg-white border-bottom py-3">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-check2-circle text-primary fs-5"></i>
                <h5 class="fw-bold mb-0">Tasks</h5>
            </div>

            <!-- Action Controls -->
            <div class="d-flex flex-wrap align-items-center gap-2">
                <!-- Status Filter -->
                <form method="GET" action="{{ route('task-lists.show', $taskList) }}" class="d-flex flex-wrap gap-2">
                    <select name="status" onchange="this.form.submit()" class="form-select form-select-sm" style="width: 130px;">
                        <option value="">All Statuses</option>
                        <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="In Progress" {{ request('status') === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="Completed" {{ request('status') === 'Completed' ? 'selected' : '' }}>Completed</option>
                    </select>

                    <select name="priority" onchange="this.form.submit()" class="form-select form-select-sm" style="width: 130px;">
                        <option value="">All Priorities</option>
                        <option value="Low" {{ request('priority') === 'Low' ? 'selected' : '' }}>Low</option>
                        <option value="Medium" {{ request('priority') === 'Medium' ? 'selected' : '' }}>Medium</option>
                        <option value="High" {{ request('priority') === 'High' ? 'selected' : '' }}>High</option>
                    </select>
                </form>

                <button class="btn btn-sm btn-jara" data-bs-toggle="modal" data-bs-target="#createTaskModal">
                    <i class="bi bi-plus-lg me-1"></i> Add Task
                </button>
            </div>
        </div>
    </div>

    <div class="card-body p-0">
        @if($tasks->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-clipboard2-check fs-1 text-muted d-block mb-2"></i>
                <p class="mb-2">No tasks found for this list.</p>
                <button class="btn btn-sm btn-jara" data-bs-toggle="modal" data-bs-target="#createTaskModal">
                    <i class="bi bi-plus-lg me-1"></i> Create the first task
                </button>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-muted">
                        <tr>
                            <th style="width: 40px;"></th>
                            <th>Title & Description</th>
                            <th>Priority</th>
                            <th>Assignee</th>
                            <th>Deadline</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tasks as $task)
                            <tr class="{{ $task->isCompleted() ? 'table-light opacity-75' : '' }}">
                                <td class="text-center">
                                    <form action="{{ route('tasks.update-status', $task) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="{{ $task->isCompleted() ? 'Pending' : 'Completed' }}">
                                        <button type="submit" class="btn btn-link p-0 text-decoration-none" title="{{ $task->isCompleted() ? 'Mark as Incomplete' : 'Mark as Completed' }}">
                                            @if($task->isCompleted())
                                                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                            @else
                                                <i class="bi bi-circle text-muted fs-5"></i>
                                            @endif
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <a href="{{ route('tasks.show', $task) }}" class="fw-semibold text-dark text-decoration-none {{ $task->isCompleted() ? 'text-decoration-line-through text-muted' : '' }}">
                                        {{ $task->title }}
                                    </a>
                                    @if($task->description)
                                        <p class="small text-muted mb-0 text-truncate" style="max-width: 320px;">
                                            {{ $task->description }}
                                        </p>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-priority-{{ $task->priority }}">
                                        {{ $task->priority }}
                                    </span>
                                </td>
                                <td>
                                    @if($task->assignee)
                                        <div class="d-flex align-items-center gap-1">
                                            <div class="avatar-circle" style="width: 26px; height: 26px; font-size: 0.7rem;">
                                                {{ strtoupper(substr($task->assignee->name, 0, 1)) }}
                                            </div>
                                            <span class="small">{{ $task->assignee->name }}</span>
                                        </div>
                                    @else
                                        <span class="small text-muted fst-italic">Unassigned</span>
                                    @endif
                                </td>
                                <td>
                                    @if($task->isOverdue())
                                        <span class="badge bg-danger">
                                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                            {{ $task->deadline->format('M d, Y') }} (Overdue)
                                        </span>
                                    @else
                                        <span class="small text-secondary">
                                            <i class="bi bi-calendar3 me-1"></i>
                                            {{ $task->deadline->format('M d, Y') }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route('tasks.update-status', $task) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" onchange="this.form.submit()" class="form-select form-select-sm py-0 px-2" style="font-size: 0.75rem; width: 120px;">
                                            <option value="Pending" {{ $task->status === 'Pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="In Progress" {{ $task->status === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                            <option value="Completed" {{ $task->status === 'Completed' ? 'selected' : '' }}>Completed</option>
                                        </select>
                                    </form>
                                </td>
                                <td class="text-end">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light p-1" type="button" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                            <li>
                                                <a class="dropdown-item small" href="{{ route('tasks.show', $task) }}">
                                                    <i class="bi bi-eye me-2 text-primary"></i> View Details
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item small" href="{{ route('tasks.edit', $task) }}">
                                                    <i class="bi bi-pencil me-2 text-warning"></i> Edit Task
                                                </a>
                                            </li>
                                            @if(Auth::user()->isAdmin() || $task->created_by === Auth::id() || $taskList->isOwnedBy(Auth::user()))
                                                <li>
                                                    <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Delete this task?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item small text-danger">
                                                            <i class="bi bi-trash me-2"></i> Delete
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @if($tasks->hasPages())
        <div class="card-footer bg-white border-top py-3 d-flex justify-content-center">
            {{ $tasks->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

<!-- Invite Member Modal -->
<div class="modal fade" id="inviteMemberModal" tabindex="-1" aria-labelledby="inviteMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom py-3">
                <h6 class="modal-title fw-bold" id="inviteMemberModalLabel"><i class="bi bi-person-plus text-primary me-2"></i> Invite Member to Team</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('task-lists.members.add', $taskList) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <p class="text-muted small">Collaborators can view tasks, update their status, and take ownership of assignments.</p>

                    <div class="mb-3">
                        <label for="user_id" class="form-label small fw-semibold text-secondary">Select Registered User</label>
                        @if($availableUsers->isEmpty())
                            <div class="alert alert-info small py-2">All registered users are already in this list or none available.</div>
                        @else
                            <select name="user_id" id="user_id" class="form-select" required>
                                <option value="">-- Choose User to Invite --</option>
                                @foreach($availableUsers as $candidate)
                                    <option value="{{ $candidate->id }}">{{ $candidate->name }} ({{ $candidate->email }})</option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                </div>
                <div class="modal-footer border-top py-2">
                    <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-jara" {{ $availableUsers->isEmpty() ? 'disabled' : '' }}>Send Invitation</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Create Task Modal -->
<div class="modal fade" id="createTaskModal" tabindex="-1" aria-labelledby="createTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom py-3">
                <h6 class="modal-title fw-bold" id="createTaskModalLabel"><i class="bi bi-plus-circle-fill text-primary me-2"></i> Add New Task</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('tasks.store') }}" method="POST">
                @csrf
                <input type="hidden" name="task_list_id" value="{{ $taskList->id }}">

                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="task_title" class="form-label small fw-semibold text-secondary">Task Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="task_title" name="title" required placeholder="What needs to be done?">
                    </div>

                    <div class="mb-3">
                        <label for="task_desc" class="form-label small fw-semibold text-secondary">Description</label>
                        <textarea class="form-control" id="task_desc" name="description" rows="3" placeholder="Provide extra context or details..."></textarea>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="task_priority" class="form-label small fw-semibold text-secondary">Priority <span class="text-danger">*</span></label>
                            <select class="form-select" id="task_priority" name="priority" required>
                                <option value="Low">Low</option>
                                <option value="Medium" selected>Medium</option>
                                <option value="High">High</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="task_status" class="form-label small fw-semibold text-secondary">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="task_status" name="status" required>
                                <option value="Pending" selected>Pending</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Completed">Completed</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="task_deadline" class="form-label small fw-semibold text-secondary">Deadline <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="task_deadline" name="deadline" value="{{ now()->addDays(3)->format('Y-m-d') }}" required>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label for="task_assignee" class="form-label small fw-semibold text-secondary">Assign To (Optional)</label>
                        <select class="form-select" id="task_assignee" name="assigned_to">
                            <option value="">-- Unassigned --</option>
                            @foreach($assignableUsers as $assignee)
                                <option value="{{ $assignee->id }}">{{ $assignee->name }} ({{ $assignee->id === Auth::id() ? 'You' : $assignee->role }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="modal-footer border-top py-2">
                    <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-jara">Create Task</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
