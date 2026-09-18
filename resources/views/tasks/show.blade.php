@extends('layouts.app')

@section('title', $task->title)
@section('page-title', 'Task Details')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
            <!-- Header bar with status and priority -->
            <div class="card-header bg-white border-bottom p-4 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge badge-priority-{{ $task->priority }} px-3 py-1">
                            {{ $task->priority }} Priority
                        </span>
                        <span class="badge badge-status-{{ str_replace(' ', '', $task->status) }} px-3 py-1">
                            {{ $task->status }}
                        </span>
                    </div>
                    <h4 class="fw-bold text-dark mb-0">{{ $task->title }}</h4>
                </div>

                <!-- Status Switcher -->
                <div class="d-flex align-items-center gap-2">
                    <form action="{{ route('tasks.update-status', $task) }}" method="POST" class="d-flex align-items-center gap-2">
                        @csrf
                        @method('PATCH')
                        <span class="small fw-semibold text-secondary">Status:</span>
                        <select name="status" onchange="this.form.submit()" class="form-select form-select-sm" style="width: 140px;">
                            <option value="Pending" {{ $task->status === 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="In Progress" {{ $task->status === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="Completed" {{ $task->status === 'Completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </form>
                </div>
            </div>

            <!-- Body Details -->
            <div class="card-body p-4">
                <div class="row g-4 mb-4">
                    <!-- Task List & Creator -->
                    <div class="col-sm-6 col-md-3">
                        <div class="text-muted small fw-semibold mb-1">Task List</div>
                        <span class="fw-bold text-dark d-block">
                            <i class="bi bi-folder2 me-1 text-primary"></i> {{ $task->taskList?->name ?? 'General' }}
                        </span>
                    </div>

                    <!-- Assignee -->
                    <div class="col-sm-6 col-md-3">
                        <div class="text-muted small fw-semibold mb-1">Assigned To</div>
                        @if($task->assignee)
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-circle" style="width: 28px; height: 28px; font-size: 0.75rem;">
                                    {{ strtoupper(substr($task->assignee->name, 0, 1)) }}
                                </div>
                                <span class="fw-semibold text-dark">{{ $task->assignee->name }}</span>
                            </div>
                        @else
                            <span class="text-muted fst-italic">Unassigned</span>
                        @endif
                    </div>

                    <!-- Deadline -->
                    <div class="col-sm-6 col-md-3">
                        <div class="text-muted small fw-semibold mb-1">Deadline</div>
                        @if($task->isOverdue())
                            <span class="badge bg-danger">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $task->deadline->format('M d, Y') }} (Overdue)
                            </span>
                        @else
                            <span class="fw-semibold text-dark">
                                <i class="bi bi-calendar-event me-1 text-primary"></i> {{ $task->deadline->format('M d, Y') }}
                            </span>
                        @endif
                    </div>

                    <!-- Created By & Date -->
                    <div class="col-sm-6 col-md-3">
                        <div class="text-muted small fw-semibold mb-1">Created By</div>
                        <div class="fw-semibold text-dark">{{ $task->creator->name }}</div>
                        <small class="text-muted">{{ $task->created_at->format('M d, Y H:i') }}</small>
                    </div>
                </div>

                <!-- Description Section -->
                <div class="p-3 bg-light rounded-3 border">
                    <h6 class="fw-bold text-secondary mb-2">Description & Notes</h6>
                    <div class="text-dark" style="white-space: pre-line;">
                        {{ $task->description ?? 'No extra description or notes provided for this task.' }}
                    </div>
                </div>
            </div>

            <!-- Footer actions -->
            <div class="card-footer bg-white border-top p-3 d-flex justify-content-between align-items-center">
                <a href="{{ route('tasks.index') }}" class="btn btn-light border">
                    <i class="bi bi-arrow-left me-1"></i> Back to Tasks
                </a>

                <div class="d-flex gap-2">
                    <a href="{{ route('tasks.edit', $task) }}" class="btn btn-outline-warning">
                        <i class="bi bi-pencil me-1"></i> Edit Task
                    </a>

                    @if($task->created_by === Auth::id() || ($task->taskList && $task->taskList->user_id === Auth::id()) || Auth::user()->isAdmin())
                        <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Delete this task permanently?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="bi bi-trash me-1"></i> Delete
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
