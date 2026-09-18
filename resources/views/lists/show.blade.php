@extends('layouts.app')

@section('title', $project->name . ' - Detail Daftar')
@section('page-title', $project->name)

@section('content')
<!-- Back Button -->
<div class="mb-3">
    <a href="{{ route('lists.index') }}" class="btn btn-sm btn-light border text-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Proyek
    </a>
</div>

<!-- Project Overview Card -->
<div class="card shadow-sm border-0 mb-4 p-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill small">
                    <i class="bi bi-folder2-open me-1"></i> Proyek Kolaborasi
                </span>
                <span class="badge bg-light text-secondary border px-3 py-1 rounded-pill small">
                    {{ $project->owner_id === auth()->id() ? 'Pemilik Proyek' : 'Kolaborator' }}
                </span>
            </div>
            <h3 class="fw-bold text-dark mb-1">{{ $project->name }}</h3>
            <p class="text-muted small mb-0">Kelola tugas tim, penugasan, dan tenggat waktu secara terpadu.</p>
        </div>

        <!-- Progress Box -->
        <div class="p-3 bg-light rounded-3 border" style="min-width: 240px;">
            <div class="d-flex justify-content-between small text-muted mb-1">
                <span class="fw-semibold">Progress Tugas</span>
                <span class="fw-bold text-primary">{{ $completedTasks }}/{{ $totalTasks }} Selesai ({{ $progress }}%)</span>
            </div>
            <div class="progress" style="height: 8px;">
                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $progress }}%;"></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Tasks List & Add Task Form -->
    <div class="col-lg-8">
        <!-- Tasks Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold text-dark mb-0">
                    <i class="bi bi-check2-square text-primary me-2"></i> Daftar Tugas ({{ $totalTasks }})
                </h6>
            </div>

            <div class="card-body p-0">
                @if($project->tasks->isEmpty())
                    <div class="empty-state-box my-4 border-0">
                        <div class="empty-state-icon">
                            <i class="bi bi-clipboard2-check"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Belum ada tugas di proyek ini</h6>
                        <p class="text-muted small mb-0">Tambahkan tugas baru menggunakan formulir di bawah ini.</p>
                    </div>
                @else
                    <div class="d-flex flex-column divide-y">
                        @foreach($project->tasks as $task)
                            @php
                                $isDone = strtolower($task->status) === 'completed';
                            @endphp
                            <div class="p-3 border-bottom d-flex align-items-center justify-content-between gap-3 {{ $isDone ? 'bg-light opacity-75' : 'bg-white' }}">
                                <div class="d-flex align-items-start gap-3">
                                    <form method="POST" action="{{ route('tasks.toggle', [$project, $task]) }}" class="mt-1">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-link p-0 text-decoration-none" title="{{ $isDone ? 'Tandai Belum Selesai' : 'Tandai Selesai' }}">
                                            @if($isDone)
                                                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                            @else
                                                <i class="bi bi-circle text-muted fs-5"></i>
                                            @endif
                                        </button>
                                    </form>

                                    <div>
                                        <div class="fw-bold text-dark {{ $isDone ? 'text-decoration-line-through text-muted' : '' }}">
                                            {{ $task->title }}
                                        </div>
                                        @if($task->description)
                                            <div class="text-muted small mb-1">{{ $task->description }}</div>
                                        @endif
                                        <div class="d-flex flex-wrap align-items-center gap-2 small">
                                            <!-- Priority -->
                                            @if(strtolower($task->priority) === 'high' || strtolower($task->priority) === 'tinggi')
                                                <span class="badge badge-priority-High px-2 py-1">Tinggi</span>
                                            @elseif(strtolower($task->priority) === 'low' || strtolower($task->priority) === 'rendah')
                                                <span class="badge badge-priority-Low px-2 py-1">Rendah</span>
                                            @else
                                                <span class="badge badge-priority-Medium px-2 py-1">Sedang</span>
                                            @endif

                                            <!-- Deadline -->
                                            <span class="text-muted">
                                                <i class="bi bi-calendar3 me-1"></i>{{ $task->deadline ? \Carbon\Carbon::parse($task->deadline)->format('d M Y') : 'Tanpa deadline' }}
                                            </span>

                                            <!-- Status -->
                                            <span class="badge {{ $isDone ? 'badge-status-Completed' : 'badge-status-Pending' }} px-2 py-1">
                                                {{ ucfirst($task->status) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <form method="POST" action="{{ route('tasks.toggle', [$project, $task]) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-outline-secondary text-nowrap">
                                        {{ $isDone ? 'Batal Selesai' : 'Selesaikan' }}
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Add Task Form Card -->
        <div class="card shadow-sm border-0 p-4">
            <h5 class="fw-bold text-dark mb-3">
                <i class="bi bi-plus-circle-fill text-primary me-2"></i> Tambah Tugas Baru
            </h5>

            <form method="POST" action="{{ route('lists.tasks.store', $project) }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-secondary" for="title">Judul Tugas <span class="text-danger">*</span></label>
                    <input id="title" name="title" class="form-control" placeholder="Contoh: Selesaikan laporan akhir" required>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-secondary" for="description">Deskripsi</label>
                    <textarea id="description" name="description" class="form-control" rows="3" placeholder="Catatan atau rincian tugas..."></textarea>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-secondary" for="priority">Prioritas</label>
                        <select id="priority" name="priority" class="form-select">
                            <option value="low">Rendah (Low)</option>
                            <option value="medium" selected>Sedang (Medium)</option>
                            <option value="high">Tinggi (High)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-secondary" for="deadline">Tenggat Waktu (Deadline)</label>
                        <input id="deadline" type="date" name="deadline" class="form-control">
                    </div>
                </div>

                <button type="submit" class="btn btn-jara-primary">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Tugas
                </button>
            </form>
        </div>
    </div>

    <!-- Right Column: Members & Add Member Form -->
    <div class="col-lg-4">
        <!-- Members List Card -->
        <div class="card shadow-sm border-0 mb-4 p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-dark mb-0">
                    <i class="bi bi-people-fill text-primary me-2"></i> Anggota Tim
                </h6>
                <span class="badge bg-light text-secondary border rounded-pill px-2 py-1 small">
                    {{ $project->members->count() }} Orang
                </span>
            </div>

            @if($project->members->isEmpty())
                <p class="text-muted small mb-0">Belum ada anggota.</p>
            @else
                <div class="d-flex flex-column gap-2 mb-3">
                    @foreach($project->members as $member)
                        <div class="d-flex align-items-center justify-content-between p-2 rounded-3 border bg-light-subtle">
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-circle" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                    {{ strtoupper(substr($member->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-bold small text-dark">{{ $member->name }}</div>
                                    <small class="text-muted" style="font-size: 0.72rem;">
                                        {{ $project->owner_id === $member->id ? 'Owner' : 'Kolaborator' }}
                                    </small>
                                </div>
                            </div>

                            @if($project->owner_id === auth()->id() && $member->id !== auth()->id())
                                <form method="POST" action="{{ route('lists.members.destroy', [$project, $member]) }}" onsubmit="return confirm('Hapus anggota {{ addslashes($member->name) }} dari proyek ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-link text-danger p-0 text-decoration-none" title="Hapus Anggota">
                                        <i class="bi bi-x-circle fs-6"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Invite Member to Project Form -->
            @if($project->owner_id === auth()->id())
                <div class="border-top pt-3 mt-2">
                    <h6 class="fw-bold text-dark small mb-2">Undang Anggota Baru</h6>
                    @if($availableUsers->isEmpty())
                        <div class="alert alert-info small py-2 mb-0">Semua pengguna sudah tergabung.</div>
                    @else
                        <form method="POST" action="{{ route('lists.members.store', $project) }}">
                            @csrf
                            <div class="mb-2">
                                <select name="user_id" class="form-select form-select-sm" required>
                                    <option value="">Pilih pengguna...</option>
                                    @foreach($availableUsers as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-sm btn-dark w-100">
                                <i class="bi bi-person-plus me-1"></i> Tambah Anggota
                            </button>
                        </form>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
