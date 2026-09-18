@extends('layouts.app')

@section('title', 'Daftar Tugas Kolaborasi')
@section('page-title', 'Team Projects & Lists')

@section('content')
<!-- Header Bar & Add Form -->
<div class="card shadow-sm border-0 mb-4 p-4">
    <div class="row align-items-center g-3">
        <div class="col-md-6">
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-collection-fill text-primary me-2"></i> Daftar Tugas & Proyek
            </h4>
            <p class="text-muted small mb-0">Kelola ruang kerja bersama untuk kolaborasi dan delegasi tugas.</p>
        </div>
        <div class="col-md-6">
            <form method="POST" action="{{ route('lists.store') }}" class="d-flex gap-2">
                @csrf
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted"><i class="bi bi-folder-plus"></i></span>
                    <input name="name" class="form-control" placeholder="Nama daftar tugas baru..." required>
                </div>
                <button type="submit" class="btn btn-jara-primary text-nowrap">
                    <i class="bi bi-plus-lg me-1"></i> Buat Daftar
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Project Cards Grid -->
@if($projects->isEmpty())
    <div class="empty-state-box my-4">
        <div class="empty-state-icon">
            <i class="bi bi-folder-x"></i>
        </div>
        <h5 class="fw-bold text-dark mb-1">Belum ada daftar tugas kolaborasi</h5>
        <p class="text-muted small mb-3">Buat daftar tugas pertama Anda menggunakan form di atas untuk mulai berkolaborasi.</p>
    </div>
@else
    <div class="row g-4 mb-4">
        @foreach($projects as $project)
            <div class="col-md-6 col-lg-4">
                <div class="card card-hover h-100 border p-4 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="avatar-circle bg-primary-subtle text-primary" style="width: 40px; height: 40px; font-size: 1.1rem;">
                            <i class="bi bi-folder2-open"></i>
                        </div>
                        <span class="badge bg-light text-secondary border px-2 py-1 small">
                            {{ $project->owner_id === auth()->id() ? 'Pemilik' : 'Kolaborator' }}
                        </span>
                    </div>

                    <h5 class="fw-bold text-dark mb-1">
                        <a href="{{ route('lists.show', $project) }}" class="text-dark text-decoration-none">
                            {{ $project->name }}
                        </a>
                    </h5>
                    <p class="text-muted small mb-4 flex-grow-1">
                        {{ $project->tasks_count }} tugas di dalam daftar ini.
                    </p>

                    <div class="border-top pt-3 mt-auto d-flex justify-content-between align-items-center">
                        <span class="small text-muted">
                            <i class="bi bi-check2-square text-primary me-1"></i> {{ $project->tasks_count }} Tasks
                        </span>
                        <a href="{{ route('lists.show', $project) }}" class="btn btn-sm btn-light border text-primary fw-semibold">
                            Buka Daftar <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
