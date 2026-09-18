@extends('layouts.app')

@section('title', 'Task Lists')
@section('page-title', 'Task Lists')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <!-- Filters & Search -->
    <div class="d-flex flex-wrap align-items-center gap-2">
        <a href="{{ route('task-lists.index') }}" class="btn btn-sm {{ !request('filter') ? 'btn-dark' : 'btn-light border' }}">
            All Lists
        </a>
        <a href="{{ route('task-lists.index', ['filter' => 'owned']) }}" class="btn btn-sm {{ request('filter') === 'owned' ? 'btn-dark' : 'btn-light border' }}">
            <i class="bi bi-person-fill me-1"></i> My Lists
        </a>
        <a href="{{ route('task-lists.index', ['filter' => 'shared']) }}" class="btn btn-sm {{ request('filter') === 'shared' ? 'btn-dark' : 'btn-light border' }}">
            <i class="bi bi-people-fill me-1"></i> Shared with Me
        </a>
    </div>

    <div class="d-flex align-items-center gap-2">
        <form method="GET" action="{{ route('task-lists.index') }}" class="d-flex align-items-center">
            @if(request('filter'))
                <input type="hidden" name="filter" value="{{ request('filter') }}">
            @endif
            <div class="input-group input-group-sm">
                <input type="text" name="search" class="form-control" placeholder="Search lists..." value="{{ request('search') }}">
                <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
            </div>
        </form>

        <button type="button" class="btn btn-sm btn-jara text-nowrap" data-bs-toggle="modal" data-bs-target="#createListModal">
            <i class="bi bi-plus-lg me-1"></i> Create List
        </button>
    </div>
</div>

@if($taskLists->isEmpty())
    <div class="card p-5 text-center shadow-sm">
        <i class="bi bi-folder-x fs-1 text-muted mb-3"></i>
        <h5 class="fw-bold">No Task Lists Found</h5>
        <p class="text-muted small">You don't have any task lists matching your filter. Create a new list to organize your team tasks!</p>
        <div class="mt-2">
            <button type="button" class="btn btn-jara" data-bs-toggle="modal" data-bs-target="#createListModal">
                <i class="bi bi-plus-lg me-1"></i> Create New Task List
            </button>
        </div>
    </div>
@else
    <div class="row g-4 mb-4">
        @foreach($taskLists as $list)
            <div class="col-md-6 col-xl-4">
                <div class="card card-hover h-100 shadow-sm d-flex flex-column">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="fw-bold mb-0">
                                <a href="{{ route('task-lists.show', $list) }}" class="text-dark text-decoration-none">
                                    {{ $list->name }}
                                </a>
                            </h5>
                            <span class="badge bg-{{ $list->user_id === Auth::id() ? 'primary' : 'info' }} bg-opacity-10 text-{{ $list->user_id === Auth::id() ? 'primary' : 'info' }} border border-{{ $list->user_id === Auth::id() ? 'primary' : 'info' }}-subtle">
                                {{ $list->user_id === Auth::id() ? 'Owner' : 'Member' }}
                            </span>
                        </div>

                        <p class="text-muted small text-truncate-2 mb-3" style="min-height: 40px;">
                            {{ $list->description ?? 'No description provided for this task group.' }}
                        </p>

                        <!-- Progress Bar -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between small text-muted mb-1">
                                <span>Progress</span>
                                <span class="fw-bold">{{ $list->progressPercentage() }}%</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar progress-bar-jara" style="width: {{ $list->progressPercentage() }}%;"></div>
                            </div>
                        </div>

                        <!-- Stats & Team -->
                        <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-auto">
                            <div class="avatar-group d-flex align-items-center">
                                <div class="avatar-circle" title="Owner: {{ $list->owner->name }}" style="border: 2px solid #4f46e5;">
                                    {{ strtoupper(substr($list->owner->name, 0, 1)) }}
                                </div>
                                @foreach($list->members->take(3) as $member)
                                    <div class="avatar-circle" title="Member: {{ $member->name }}">
                                        {{ strtoupper(substr($member->name, 0, 1)) }}
                                    </div>
                                @endforeach
                                @if($list->members->count() > 3)
                                    <span class="small text-muted ms-3">+{{ $list->members->count() - 3 }}</span>
                                @endif
                            </div>

                            <div class="small fw-semibold text-secondary">
                                <i class="bi bi-check2-square me-1"></i> {{ $list->tasks_count }} Tasks
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-white border-top py-2 px-3 d-flex justify-content-between align-items-center">
                        <a href="{{ route('task-lists.show', $list) }}" class="btn btn-sm btn-link text-primary p-0 text-decoration-none fw-semibold">
                            View Details <i class="bi bi-arrow-right"></i>
                        </a>

                        @if(Auth::user()->isAdmin() || $list->isOwnedBy(Auth::user()))
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light p-1" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                    <li>
                                        <a class="dropdown-item small" href="{{ route('task-lists.edit', $list) }}">
                                            <i class="bi bi-pencil me-2 text-warning"></i> Edit List
                                        </a>
                                    </li>
                                    <li>
                                        <form action="{{ route('task-lists.destroy', $list) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this list and all its tasks?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item small text-danger">
                                                <i class="bi bi-trash me-2"></i> Delete List
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-center">
        {{ $taskLists->links('pagination::bootstrap-5') }}
    </div>
@endif

<!-- Create Task List Modal -->
<div class="modal fade" id="createListModal" tabindex="-1" aria-labelledby="createListModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom py-3">
                <h6 class="modal-title fw-bold" id="createListModalLabel"><i class="bi bi-folder-plus text-primary me-2"></i> Create New Task List</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('task-lists.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="modal_list_name" class="form-label small fw-semibold text-secondary">List Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="modal_list_name" name="name" required placeholder="e.g. Website Redesign Q3">
                    </div>
                    <div class="mb-0">
                        <label for="modal_list_description" class="form-label small fw-semibold text-secondary">Description</label>
                        <textarea class="form-control" id="modal_list_description" name="description" rows="3" placeholder="Brief objective of this task list..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top py-2">
                    <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-jara">Create List</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
