@extends('layouts.app')

@section('title', 'Create Task')
@section('page-title', 'Create Task')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-plus-circle-fill text-primary me-2"></i> New Task</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('tasks.store') }}" method="POST">
                    @csrf

                    <!-- Task List Selection -->
                    <div class="mb-3">
                        <label for="task_list_id" class="form-label small fw-semibold text-secondary">Task List <span class="text-danger">*</span></label>
                        <select class="form-select @error('task_list_id') is-invalid @enderror" id="task_list_id" name="task_list_id" required>
                            <option value="">-- Choose Task List --</option>
                            @foreach($taskLists as $list)
                                <option value="{{ $list->id }}" {{ (string) old('task_list_id', $selectedListId) === (string) $list->id ? 'selected' : '' }}>
                                    {{ $list->name }} {{ $list->owner ? '(Owner: ' . $list->owner->name . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('task_list_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Title -->
                    <div class="mb-3">
                        <label for="title" class="form-label small fw-semibold text-secondary">Task Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required placeholder="e.g. Implement user registration flow">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label small fw-semibold text-secondary">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" placeholder="Detailed instructions, requirements, or notes...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <!-- Priority -->
                        <div class="col-md-4">
                            <label for="priority" class="form-label small fw-semibold text-secondary">Priority <span class="text-danger">*</span></label>
                            <select class="form-select @error('priority') is-invalid @enderror" id="priority" name="priority" required>
                                <option value="Low" {{ old('priority') === 'Low' ? 'selected' : '' }}>Low</option>
                                <option value="Medium" {{ old('priority', 'Medium') === 'Medium' ? 'selected' : '' }}>Medium</option>
                                <option value="High" {{ old('priority') === 'High' ? 'selected' : '' }}>High</option>
                            </select>
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="col-md-4">
                            <label for="status" class="form-label small fw-semibold text-secondary">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="Pending" {{ old('status', 'Pending') === 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="In Progress" {{ old('status') === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="Completed" {{ old('status') === 'Completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Deadline -->
                        <div class="col-md-4">
                            <label for="deadline" class="form-label small fw-semibold text-secondary">Deadline <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('deadline') is-invalid @enderror" id="deadline" name="deadline" value="{{ old('deadline', now()->addDays(3)->format('Y-m-d')) }}" required>
                            @error('deadline')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Assignee -->
                    <div class="mb-4">
                        <label for="assigned_to" class="form-label small fw-semibold text-secondary">Assign To (Optional)</label>
                        <select class="form-select @error('assigned_to') is-invalid @enderror" id="assigned_to" name="assigned_to">
                            <option value="">-- Unassigned --</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" {{ (string) old('assigned_to') === (string) $u->id ? 'selected' : '' }}>
                                    {{ $u->name }} ({{ $u->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('assigned_to')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('tasks.index') }}" class="btn btn-light border">Cancel</a>
                        <button type="submit" class="btn btn-jara">
                            <i class="bi bi-check-lg me-1"></i> Create Task
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
