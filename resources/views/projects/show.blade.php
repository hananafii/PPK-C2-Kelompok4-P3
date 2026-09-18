@extends('layouts.app')

@section('title', $project->name . ' · JARA')
@section('page-title', 'Legacy Project Collaboration')

@section('content')
<!-- Back to Lists -->
<div class="mb-3">
    <a href="{{ route('lists.index') }}" class="btn btn-sm btn-light border text-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Project Lists
    </a>
</div>

<!-- Project Overview Card -->
<div class="card shadow-sm border-0 mb-4 p-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill small">
                    <i class="bi bi-folder2-open me-1"></i> Project Space
                </span>
            </div>
            <h3 class="fw-bold text-dark mb-1">{{ $project->name }}</h3>
            <p class="text-muted small mb-0">Manage collaborative project members and task milestones.</p>
        </div>

        <!-- Progress Box with exact test string -->
        <div class="p-3 bg-light rounded-3 border" style="min-width: 250px;">
            <div class="d-flex justify-content-between small text-muted mb-1">
                <span class="fw-semibold">Progress</span>
                <span class="fw-bold text-primary">{{ $progress }}%</span>
            </div>
            <div class="progress mb-2" style="height: 8px;">
                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $progress }}%;"></div>
            </div>
            <p class="progress-copy small text-muted mb-0">{{ $completedTasks }} of {{ $totalTasks }} tasks completed</p>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Members List -->
    <div class="col-lg-7">
        <div class="card shadow-sm border-0 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold text-dark mb-0" id="members-title">
                    <i class="bi bi-people-fill text-primary me-2"></i> Members
                </h5>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1">
                    {{ $project->members->count() }} Members
                </span>
            </div>

            @if ($project->members->isEmpty())
                <div class="empty-state-box my-3 py-4">
                    <div class="empty-state-icon" style="width: 48px; height: 48px; font-size: 1.5rem;">
                        <i class="bi bi-people"></i>
                    </div>
                    <p class="text-muted small mb-0">Belum ada member di project ini.</p>
                </div>
            @else
                <div class="d-flex flex-column gap-2">
                    @foreach ($project->members as $member)
                        <div class="d-flex align-items-center justify-content-between p-3 rounded-3 border bg-white card-hover">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-circle" style="width: 38px; height: 38px; font-size: 0.9rem;">
                                    {{ strtoupper(substr($member->name, 0, 1)) }}
                                </div>
                                <span class="fw-bold text-dark member-name">{{ $member->name }}</span>
                            </div>

                            <form method="POST" action="{{ route('projects.members.destroy', [$project, $member]) }}" onsubmit="return confirm('Hapus member ini dari project?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit">
                                    <i class="bi bi-person-x me-1"></i> Remove
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Right Column: Add Member Section -->
    <div class="col-lg-5">
        <div class="card shadow-sm border-0 p-4">
            <h5 class="fw-bold text-dark mb-2">
                <i class="bi bi-person-plus-fill text-primary me-2"></i> Add Member
            </h5>
            <p class="text-muted small mb-3">Invite another user to join this project.</p>

            @if($availableUsers->isEmpty())
                <div class="alert alert-info small rounded-3 mb-0">
                    <i class="bi bi-info-circle me-1"></i> All users are already members of this project.
                </div>
            @else
                <form method="POST" action="{{ route('projects.members.store', $project) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary" for="user_id">Select User</label>
                        <select id="user_id" name="user_id" class="form-select" required>
                            <option value="">Pilih user</option>
                            @foreach ($availableUsers as $user)
                                <option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <button class="btn btn-jara-primary w-100" type="submit">
                        <i class="bi bi-plus-lg me-1"></i> Add Member
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
