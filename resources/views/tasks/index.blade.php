@extends('layouts.app')

@section('title', 'Tasks Management & Dashboard')
@section('page-title', 'Tasks Management')

@section('content')
@php
    $allUserTasks = $tasks->getCollection();
    $totalCount = $tasks->total();
    $completedCount = $allUserTasks->where('status', 'Completed')->count();
    $inProgressCount = $allUserTasks->where('status', 'In Progress')->count();
    $pendingCount = $allUserTasks->where('status', 'Pending')->count();
    $now = now()->startOfDay();
    $overdueCount = $allUserTasks->filter(function($t) use ($now) {
        return $t->deadline && \Carbon\Carbon::parse($t->deadline)->startOfDay()->lt($now) && $t->status !== 'Completed';
    })->count();
    $completionRate = $allUserTasks->count() > 0 ? (int) round(($completedCount / $allUserTasks->count()) * 100) : 0;
@endphp

<!-- Dashboard Productivity Statistics -->
<div class="row g-3 mb-4">
    <!-- Total Tasks Card -->
    <div class="col-6 col-lg-3">
        <div class="card card-hover h-100 p-3">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-bold text-uppercase tracking-wider">Total Tasks</span>
                <div class="avatar-circle bg-primary-subtle text-primary" style="width: 34px; height: 34px;">
                    <i class="bi bi-kanban-fill"></i>
                </div>
            </div>
            <div class="fs-3 fw-extrabold text-dark lh-1 mb-2">{{ $totalCount }}</div>
            <div class="d-flex align-items-center gap-2">
                <div class="progress flex-grow-1" style="height: 6px;">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $completionRate }}%"></div>
                </div>
                <span class="small text-muted fw-bold">{{ $completionRate }}%</span>
            </div>
        </div>
    </div>

    <!-- Completed Tasks Card -->
    <div class="col-6 col-lg-3">
        <div class="card card-hover h-100 p-3">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-bold text-uppercase tracking-wider">Completed</span>
                <div class="avatar-circle bg-success-subtle text-success" style="width: 34px; height: 34px;">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
            </div>
            <div class="fs-3 fw-extrabold text-success lh-1 mb-2">{{ $completedCount }}</div>
            <small class="text-muted"><i class="bi bi-arrow-up-right text-success me-1"></i> Finished tasks</small>
        </div>
    </div>

    <!-- Pending & In Progress Card -->
    <div class="col-6 col-lg-3">
        <div class="card card-hover h-100 p-3">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-bold text-uppercase tracking-wider">Pending / Active</span>
                <div class="avatar-circle bg-info-subtle text-info-emphasis" style="width: 34px; height: 34px;">
                    <i class="bi bi-hourglass-split"></i>
                </div>
            </div>
            <div class="fs-3 fw-extrabold text-dark lh-1 mb-2">{{ $inProgressCount + $pendingCount }}</div>
            <small class="text-muted">
                <span class="text-primary fw-semibold">{{ $inProgressCount }} In Progress</span> &bull; {{ $pendingCount }} Pending
            </small>
        </div>
    </div>

    <!-- Overdue Tasks Card -->
    <div class="col-6 col-lg-3">
        <div class="card card-hover h-100 p-3 {{ $overdueCount > 0 ? 'border-danger-subtle bg-danger-subtle bg-opacity-10' : '' }}">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-bold text-uppercase tracking-wider">Overdue</span>
                <div class="avatar-circle {{ $overdueCount > 0 ? 'bg-danger text-white' : 'bg-secondary-subtle text-secondary' }}" style="width: 34px; height: 34px;">
                    <i class="bi bi-exclamation-octagon-fill"></i>
                </div>
            </div>
            <div class="fs-3 fw-extrabold {{ $overdueCount > 0 ? 'text-danger' : 'text-muted' }} lh-1 mb-2">{{ $overdueCount }}</div>
            <small class="{{ $overdueCount > 0 ? 'text-danger fw-semibold' : 'text-muted' }}">
                {{ $overdueCount > 0 ? 'Requires immediate action' : 'All deadlines on track' }}
            </small>
        </div>
    </div>
</div>

<!-- Filter Bar (Modern SaaS Linear-style) -->
<div class="card shadow-sm border-0 mb-4 p-3">
    <form method="GET" action="{{ route('tasks.index') }}" class="row g-2 align-items-center">
        <!-- Search Input -->
        <div class="col-md-3">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Search tasks..." value="{{ request('search') }}">
            </div>
        </div>

        <!-- Task List Filter -->
        <div class="col-md-3">
            <select name="task_list_id" class="form-select form-select-sm">
                <option value="">-- All Task Lists --</option>
                @foreach($accessibleLists as $list)
                    <option value="{{ $list->id }}" {{ (string) request('task_list_id') === (string) $list->id ? 'selected' : '' }}>
                        {{ $list->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Priority Filter -->
        <div class="col-md-2">
            <select name="priority" class="form-select form-select-sm">
                <option value="">-- Priority --</option>
                <option value="High" {{ request('priority') === 'High' ? 'selected' : '' }}>High Priority</option>
                <option value="Medium" {{ request('priority') === 'Medium' ? 'selected' : '' }}>Medium Priority</option>
                <option value="Low" {{ request('priority') === 'Low' ? 'selected' : '' }}>Low Priority</option>
            </select>
        </div>

        <!-- Status Filter -->
        <div class="col-md-2">
            <select name="status" class="form-select form-select-sm">
                <option value="">-- Status --</option>
                <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="In Progress" {{ request('status') === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                <option value="Completed" {{ request('status') === 'Completed' ? 'selected' : '' }}>Completed</option>
            </select>
        </div>

        <!-- Action Buttons -->
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-sm btn-dark w-100 fw-semibold">Filter</button>
            <a href="{{ route('tasks.index') }}" class="btn btn-sm btn-light border" title="Reset Filters"><i class="bi bi-arrow-counterclockwise"></i></a>
        </div>

        <!-- Checkbox: Assigned to me -->
        <div class="col-12 mt-2">
            <div class="form-check form-check-inline small">
                <input class="form-check-input" type="checkbox" id="assigned_to_me" name="assigned_to_me" value="1" {{ request()->boolean('assigned_to_me') ? 'checked' : '' }} onchange="this.form.submit()">
                <label class="form-check-label text-secondary fw-semibold" for="assigned_to_me">
                    <i class="bi bi-person-check me-1"></i> Only show tasks assigned to me
                </label>
            </div>
        </div>
    </form>
</div>

<!-- Header Section with count and New Task button -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="fw-bold text-dark mb-0">
            <i class="bi bi-check2-square text-primary me-2"></i> Tasks Overview ({{ $tasks->total() }})
        </h5>
        <small class="text-muted">Organize, track, and complete tasks with your team</small>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('tasks.create') }}" class="btn btn-sm btn-jara-primary d-inline-flex align-items-center gap-1">
            <i class="bi bi-plus-lg"></i>
            <span>New Task</span>
        </a>
    </div>
</div>

<!-- Tasks Cards Container (Todoist/Linear style) -->
@if($tasks->isEmpty())
    <!-- Empty State -->
    <div class="empty-state-box my-4">
        <div class="empty-state-icon">
            <i class="bi bi-clipboard2-check"></i>
        </div>
        <h5 class="fw-bold text-dark mb-1">No tasks yet. Create your first task.</h5>
        <p class="text-muted small mb-3" style="max-width: 400px; margin: 0 auto;">
            Get organized and start tracking your work by creating a new task with deadlines and priorities.
        </p>
        <a href="{{ route('tasks.create') }}" class="btn btn-sm btn-jara-primary px-3">
            <i class="bi bi-plus-lg me-1"></i> Create Task
        </a>
    </div>
@else
    <div class="d-flex flex-column gap-2 mb-4">
        @foreach($tasks as $task)
            @php
                $isOverdue = $task->deadline && \Carbon\Carbon::parse($task->deadline)->startOfDay()->lt($now) && $task->status !== 'Completed';
            @endphp
            <div class="card card-hover border p-3 {{ $task->isCompleted() ? 'bg-light opacity-75' : 'bg-white' }}">
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                    <!-- Left: Checkbox Toggle & Title/Description -->
                    <div class="d-flex align-items-start gap-3 flex-grow-1">
                        <!-- Quick Mark Complete Toggle -->
                        <form action="{{ route('tasks.update-status', $task) }}" method="POST" class="mt-1">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="{{ $task->isCompleted() ? 'Pending' : 'Completed' }}">
                            <button type="submit" class="btn btn-link p-0 text-decoration-none" title="{{ $task->isCompleted() ? 'Mark as Incomplete' : 'Mark as Completed' }}">
                                @if($task->isCompleted())
                                    <i class="bi bi-check-circle-fill text-success fs-4"></i>
                                @else
                                    <i class="bi bi-circle text-muted fs-4"></i>
                                @endif
                            </button>
                        </form>

                        <div>
                            <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                <a href="{{ route('tasks.show', $task) }}" class="fw-bold text-dark text-decoration-none fs-6 {{ $task->isCompleted() ? 'text-decoration-line-through text-muted' : '' }}">
                                    {{ $task->title }}
                                </a>
                                @if($task->taskList)
                                    <span class="badge bg-light text-secondary border">
                                        <i class="bi bi-folder2 me-1"></i>{{ $task->taskList->name }}
                                    </span>
                                @endif
                            </div>

                            @if($task->description)
                                <p class="text-secondary small mb-2 text-truncate" style="max-width: 650px;">
                                    {{ $task->description }}
                                </p>
                            @endif

                            <!-- Meta Chips: Priority, Deadline, Assignee -->
                            <div class="d-flex flex-wrap align-items-center gap-2 small">
                                <!-- Priority Badge -->
                                @if($task->priority === 'High')
                                    <span class="badge badge-priority-High px-2 py-1">
                                        <i class="bi bi-flag-fill me-1"></i> HIGH
                                    </span>
                                @elseif($task->priority === 'Medium')
                                    <span class="badge badge-priority-Medium px-2 py-1">
                                        <i class="bi bi-flag-fill me-1"></i> MEDIUM
                                    </span>
                                @else
                                    <span class="badge badge-priority-Low px-2 py-1">
                                        <i class="bi bi-flag-fill me-1"></i> LOW
                                    </span>
                                @endif

                                <!-- Status Badge -->
                                @if($task->status === 'Completed')
                                    <span class="badge badge-status-Completed px-2 py-1">
                                        <i class="bi bi-check-lg me-1"></i> Completed
                                    </span>
                                @elseif($task->status === 'In Progress')
                                    <span class="badge badge-status-InProgress px-2 py-1">
                                        <i class="bi bi-play-fill me-1"></i> In Progress
                                    </span>
                                @else
                                    <span class="badge badge-status-Pending px-2 py-1">
                                        <i class="bi bi-clock me-1"></i> Pending
                                    </span>
                                @endif

                                <!-- Deadline Indicator with Calendar Icon -->
                                @if($task->deadline)
                                    <span class="badge {{ $isOverdue ? 'bg-danger text-white' : 'bg-light text-muted border' }} px-2 py-1">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        {{ \Carbon\Carbon::parse($task->deadline)->format('d M Y') }}
                                        @if($isOverdue)
                                            (Overdue)
                                        @endif
                                    </span>
                                @endif

                                <!-- Assignee Info -->
                                @if($task->assignee)
                                    <span class="text-muted d-inline-flex align-items-center gap-1" title="Assigned to {{ $task->assignee->name }}">
                                        <div class="avatar-circle" style="width: 20px; height: 20px; font-size: 0.65rem;">
                                            {{ strtoupper(substr($task->assignee->name, 0, 1)) }}
                                        </div>
                                        <span>{{ $task->assignee->name }}</span>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Right: Action Buttons -->
                    <div class="d-flex align-items-center gap-2 ms-auto flex-shrink-0">
                        <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-light border" title="View Task Details">
                            <i class="bi bi-eye"></i> View
                        </a>
                        <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-light border" title="Edit Task">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete task \'{{ addslashes($task->title) }}\'?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Task">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-4">
        <small class="text-muted">
            Showing {{ $tasks->firstItem() ?? 0 }} to {{ $tasks->lastItem() ?? 0 }} of {{ $tasks->total() }} tasks
        </small>
        <div>
            {{ $tasks->links() }}
        </div>
    </div>
@endif

@endsection
