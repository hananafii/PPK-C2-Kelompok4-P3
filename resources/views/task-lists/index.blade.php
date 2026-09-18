@extends('layouts.app')

@section('title', 'Task Lists & Projects')
@section('page-title', 'Task Lists')

@section('content')
<!-- Header Bar & Filters -->
<div class="card shadow-sm border-0 mb-4 p-3">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <!-- Search & Quick Filters -->
        <form method="GET" action="{{ route('task-lists.index') }}" class="d-flex flex-wrap gap-2 align-items-center flex-grow-1">
            <div class="input-group input-group-sm" style="max-width: 280px;">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control bg-light border-start-0" placeholder="Search task lists..." value="{{ request('search') }}">
            </div>

            <div class="btn-group btn-group-sm" role="group">
                <a href="{{ route('task-lists.index') }}" class="btn btn-outline-secondary {{ !request('filter') ? 'active' : '' }}">All</a>
                <a href="{{ route('task-lists.index', ['filter' => 'owned']) }}" class="btn btn-outline-secondary {{ request('filter') === 'owned' ? 'active' : '' }}">My Lists</a>
                <a href="{{ route('task-lists.index', ['filter' => 'shared']) }}" class="btn btn-outline-secondary {{ request('filter') === 'shared' ? 'active' : '' }}">Shared</a>
            </div>

            @if(request()->anyFilled(['search', 'filter']))
                <a href="{{ route('task-lists.index') }}" class="btn btn-sm btn-light border" title="Reset Filters"><i class="bi bi-arrow-counterclockwise"></i></a>
            @endif
        </form>

        <a href="{{ route('task-lists.create') }}" class="btn btn-sm btn-jara-primary text-nowrap d-inline-flex align-items-center gap-1">
            <i class="bi bi-plus-lg"></i>
            <span>Create New List</span>
        </a>
    </div>
</div>

@if($taskLists->isEmpty())
    <!-- Empty State -->
    <div class="empty-state-box my-4">
        <div class="empty-state-icon">
            <i class="bi bi-folder-plus"></i>
        </div>
        <h5 class="fw-bold text-dark mb-1">No task lists yet. Create your first list.</h5>
        <p class="text-muted small mb-3" style="max-width: 440px; margin: 0 auto;">
            Organize tasks into structured project boards, invite team members, and track completion progress.
        </p>
        <a href="{{ route('task-lists.create') }}" class="btn btn-sm btn-jara-primary px-3">
            <i class="bi bi-plus-lg me-1"></i> Create New Task List
        </a>
    </div>
@else
    <!-- Project-style Cards Grid (Notion / Linear / Trello aesthetic) -->
    <div class="row g-4 mb-4">
        @foreach($taskLists as $list)
            @php
                $isOwner = $list->user_id === Auth::id();
                $progress = $list->progressPercentage();
            @endphp
            <div class="col-md-6 col-xl-4">
                <div class="card card-hover h-100 border d-flex flex-column">
                    <div class="card-body p-4 d-flex flex-column">
                        <!-- Top: List Name & Role Badge -->
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="fw-bold mb-0">
                                <a href="{{ route('task-lists.show', $list) }}" class="text-dark text-decoration-none">
                                    {{ $list->name }}
                                </a>
                            </h5>
                            <span class="badge {{ $isOwner ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-info-subtle text-info-emphasis border border-info-subtle' }} rounded-pill px-2 py-1 small">
                                <i class="bi {{ $isOwner ? 'bi-shield-check' : 'bi-people' }} me-1"></i>
                                {{ $isOwner ? 'Owner' : 'Member' }}
                            </span>
                        </div>

                        <!-- Description -->
                        <p class="text-muted small mb-3 flex-grow-1" style="min-height: 42px;">
                            {{ $list->description ? \Illuminate\Support\Str::limit($list->description, 110) : 'No description provided for this task group.' }}
                        </p>

                        <!-- Progress Bar with Percentage -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between small text-muted mb-1">
                                <span class="fw-semibold">Progress</span>
                                <span class="fw-bold text-dark">{{ $progress }}%</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $progress }}%;"></div>
                            </div>
                        </div>

                        <!-- Meta: Team Avatars & Task Count -->
                        <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-auto">
                            <!-- Team Avatars -->
                            <div class="d-flex align-items-center gap-1">
                                <div class="avatar-circle" title="Owner: {{ $list->owner?->name ?? 'User' }}" style="border: 2px solid #4f46e5; width: 28px; height: 28px;">
                                    {{ strtoupper(substr($list->owner?->name ?? 'U', 0, 1)) }}
                                </div>
                                @if(isset($list->members))
                                    @foreach($list->members->take(2) as $member)
                                        <div class="avatar-circle" title="Member: {{ $member->name }}" style="width: 28px; height: 28px;">
                                            {{ strtoupper(substr($member->name, 0, 1)) }}
                                        </div>
                                    @endforeach
                                    @if($list->members->count() > 2)
                                        <span class="small text-muted ms-1" style="font-size: 0.75rem;">+{{ $list->members->count() - 2 }}</span>
                                    @endif
                                @endif
                            </div>

                            <!-- Number of Tasks Badge -->
                            <div class="small fw-semibold text-secondary">
                                <i class="bi bi-check2-square text-primary me-1"></i>
                                {{ $list->tasks_count ?? $list->tasks()->count() }} Tasks
                            </div>
                        </div>
                    </div>

                    <!-- Card Footer Actions -->
                    <div class="card-footer bg-white border-top py-2 px-4 d-flex justify-content-between align-items-center">
                        <a href="{{ route('task-lists.show', $list) }}" class="btn btn-sm btn-link text-primary p-0 text-decoration-none fw-semibold">
                            Open List <i class="bi bi-arrow-right ms-1"></i>
                        </a>

                        <div class="dropdown">
                            <button class="btn btn-sm btn-light border p-1 px-2" type="button" data-bs-toggle="dropdown" title="Actions">
                                <i class="bi bi-three-dots"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                                <li>
                                    <a class="dropdown-item small" href="{{ route('task-lists.show', $list) }}">
                                        <i class="bi bi-eye me-2 text-primary"></i> View Tasks
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item small" href="{{ route('task-lists.members.index', $list) }}">
                                        <i class="bi bi-people me-2 text-info"></i> Manage Members
                                    </a>
                                </li>
                                @if(Auth::user()?->isAdmin() || $list->isOwnedBy(Auth::user()))
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item small" href="{{ route('task-lists.edit', $list) }}">
                                            <i class="bi bi-pencil me-2 text-warning"></i> Edit List
                                        </a>
                                    </li>
                                    <li>
                                        <form action="{{ route('task-lists.destroy', $list) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete \'{{ addslashes($list->name) }}\' and all its tasks? This action is atomic.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item small text-danger">
                                                <i class="bi bi-trash3 me-2"></i> Delete List
                                            </button>
                                        </form>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $taskLists->links('pagination::bootstrap-5') }}
    </div>
@endif
@endsection
