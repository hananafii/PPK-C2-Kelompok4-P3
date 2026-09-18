@extends('layouts.app')

@section('title', 'All Tasks')
@section('page-title', 'All Tasks')

@section('content')
<!-- Filter bar -->
<div class="card shadow-sm border-0 mb-4 p-3">
    <form method="GET" action="{{ route('tasks.index') }}" class="row g-2 align-items-center">
        <!-- Search Input -->
        <div class="col-md-3">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Search tasks..." value="{{ request('search') }}">
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
            <button type="submit" class="btn btn-sm btn-dark w-100">Filter</button>
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

<!-- Tasks Table -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0">
            <i class="bi bi-check2-square text-primary me-2"></i> Tasks Overview ({{ $tasks->total() }})
        </h6>
        <a href="{{ route('tasks.create') }}" class="btn btn-sm btn-jara">
            <i class="bi bi-plus-lg me-1"></i> New Task
        </a>
    </div>

    <div class="card-body p-0">
        @if($tasks->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-clipboard2-x fs-1 text-muted d-block mb-2"></i>
                <p class="mb-2">No tasks found matching your criteria.</p>
                <a href="{{ route('tasks.create') }}" class="btn btn-sm btn-jara">Create a New Task</a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-muted">
                        <tr>
                            <th style="width: 40px;"></th>
                            <th>Task Title</th>
                            <th>List</th>
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
                                        <p class="small text-muted mb-0 text-truncate" style="max-width: 250px;">{{ $task->description }}</p>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-secondary border">
                                        <i class="bi bi-collection me-1"></i>{{ $task->taskList?->name ?? 'General' }}
                                    </span>
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
                                            @if($task->created_by === Auth::id() || ($task->taskList && $task->taskList->user_id === Auth::id()) || Auth::user()->isAdmin())
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
@endsection
