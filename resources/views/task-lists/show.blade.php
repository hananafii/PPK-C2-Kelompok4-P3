@extends('layouts.app')

@section('title', $taskList->name)
@section('page-title', 'Task List Overview')

@section('content')
<!-- Header Card -->
<div class="card shadow-sm border-0 mb-4 p-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
        <div>
            <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill small">
                    <i class="bi bi-folder2-open me-1"></i> Task List
                </span>
                <span class="badge bg-light text-secondary border px-3 py-1 rounded-pill small">
                    Owner: <strong>{{ $taskList->owner?->name ?? 'You' }}</strong>
                </span>
                @if(isset($taskList->members) && $taskList->members->count() > 0)
                    <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-3 py-1 rounded-pill small">
                        <i class="bi bi-people me-1"></i> {{ $taskList->members->count() }} Collaborators
                    </span>
                @endif
            </div>
            <h3 class="fw-bold text-dark mb-1">{{ $taskList->name }}</h3>
            <p class="text-muted small mb-0">{{ $taskList->description ?? 'No description provided.' }}</p>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            <a href="{{ route('tasks.create', ['task_list_id' => $taskList->id]) }}" class="btn btn-sm btn-jara-primary d-inline-flex align-items-center gap-1">
                <i class="bi bi-plus-lg"></i> Add Task
            </a>
            <a href="{{ route('task-lists.members.index', $taskList) }}" class="btn btn-sm btn-light border text-secondary d-inline-flex align-items-center gap-1">
                <i class="bi bi-people-fill text-primary"></i> Members
            </a>
            @if(Auth::user()?->isAdmin() || $taskList->isOwnedBy(Auth::user()))
                <a href="{{ route('task-lists.edit', $taskList) }}" class="btn btn-sm btn-light border" title="Edit List">
                    <i class="bi bi-pencil"></i> Edit
                </a>
                <form action="{{ route('task-lists.destroy', $taskList) }}" method="POST" onsubmit="return confirm('Delete this task list and all its tasks permanently? This action is atomic.');" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete List">
                        <i class="bi bi-trash3"></i>
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Progress & Metrics Summary -->
    <div class="row g-3 mt-3 pt-3 border-top">
        <div class="col-6 col-md-3">
            <div class="small text-muted fw-semibold mb-1">Progress</div>
            <div class="d-flex align-items-center gap-2">
                <div class="progress flex-grow-1" style="height: 8px;">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $progress }}%;"></div>
                </div>
                <span class="fs-6 fw-bold text-dark">{{ $progress }}%</span>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="small text-muted fw-semibold mb-1">Total Tasks</div>
            <div class="fs-5 fw-bold text-dark">{{ $totalCount }} Tasks</div>
        </div>
        <div class="col-6 col-md-3">
            <div class="small text-muted fw-semibold mb-1">Completed</div>
            <div class="fs-5 fw-bold text-success">{{ $completedCount }} Done</div>
        </div>
        <div class="col-6 col-md-3">
            <div class="small text-muted fw-semibold mb-1">Active / Pending</div>
            <div class="fs-5 fw-bold text-primary">{{ $pendingCount + $inProgressCount }} In Progress</div>
        </div>
    </div>
</div>

<!-- Tasks Listing -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0 text-dark">
            <i class="bi bi-check2-square text-primary me-2"></i> Tasks in this List
        </h6>
        <a href="{{ route('task-lists.index') }}" class="btn btn-sm btn-light border">
            <i class="bi bi-arrow-left me-1"></i> Back to Lists
        </a>
    </div>

    <div class="card-body p-0">
        @if($tasks->isEmpty())
            <div class="empty-state-box my-4 border-0">
                <div class="empty-state-icon">
                    <i class="bi bi-clipboard-check"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">No tasks in this list yet</h5>
                <p class="text-muted small mb-3">Add tasks to track your milestones and deadlines inside {{ $taskList->name }}.</p>
                <a href="{{ route('tasks.create', ['task_list_id' => $taskList->id]) }}" class="btn btn-sm btn-jara-primary px-3">
                    <i class="bi bi-plus-lg me-1"></i> Add First Task
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-muted">
                        <tr>
                            <th class="ps-4">Title</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Deadline</th>
                            <th>Assignee</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tasks as $task)
                            <tr>
                                <td class="ps-4">
                                    <a href="{{ route('tasks.show', $task) }}" class="fw-bold text-dark text-decoration-none">
                                        {{ $task->title }}
                                    </a>
                                    @if($task->description)
                                        <div class="text-muted small text-truncate" style="max-width: 300px;">
                                            {{ $task->description }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if($task->status === 'Completed')
                                        <span class="badge badge-status-Completed px-2 py-1">Completed</span>
                                    @elseif($task->status === 'In Progress')
                                        <span class="badge badge-status-InProgress px-2 py-1">In Progress</span>
                                    @else
                                        <span class="badge badge-status-Pending px-2 py-1">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    @if($task->priority === 'High')
                                        <span class="badge badge-priority-High px-2 py-1">High</span>
                                    @elseif($task->priority === 'Medium')
                                        <span class="badge badge-priority-Medium px-2 py-1">Medium</span>
                                    @else
                                        <span class="badge badge-priority-Low px-2 py-1">Low</span>
                                    @endif
                                </td>
                                <td>
                                    @if($task->deadline)
                                        <span class="small text-muted">
                                            <i class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::parse($task->deadline)->format('d M Y') }}
                                        </span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($task->assignee)
                                        <span class="d-flex align-items-center gap-1 small text-dark">
                                            <div class="avatar-circle" style="width: 22px; height: 22px; font-size: 0.65rem;">
                                                {{ strtoupper(substr($task->assignee->name, 0, 1)) }}
                                            </div>
                                            <span>{{ $task->assignee->name }}</span>
                                        </span>
                                    @else
                                        <span class="text-muted small">Unassigned</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-light border p-1 px-2" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-light border p-1 px-2" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-3 border-top d-flex justify-content-center">
                {{ $tasks->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection
