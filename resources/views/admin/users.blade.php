@extends('layouts.app')

@section('title', 'Kelola Pengguna - Admin')
@section('page-title', 'User Administration')

@section('content')
<div class="row g-4">
    <!-- Left Column: Add User Card -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 p-4">
            <h5 class="fw-bold text-dark mb-2">
                <i class="bi bi-person-plus-fill text-primary me-2"></i> Tambah Pengguna
            </h5>
            <p class="text-muted small mb-4">
                Daftarkan akun pengguna baru ke dalam sistem JARA.
            </p>

            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-secondary" for="name">Nama Lengkap</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-person"></i></span>
                        <input id="name" name="name" type="text" class="form-control" placeholder="Nama Lengkap" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-secondary" for="email">Alamat Email</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-envelope"></i></span>
                        <input id="email" name="email" type="email" class="form-control" placeholder="nama@email.com" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-semibold text-secondary" for="password">Password</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-lock"></i></span>
                        <input id="password" name="password" type="password" class="form-control" placeholder="Minimal 6 karakter" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-jara-primary w-100">
                    <i class="bi bi-check2-circle me-1"></i> Tambah Pengguna
                </button>
            </form>
        </div>
    </div>

    <!-- Right Column: User Management Table -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold text-dark mb-0">Daftar Pengguna</h5>
                    <small class="text-muted">Total {{ count($users) }} akun terdaftar dalam sistem</small>
                </div>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1">
                    {{ count($users) }} Users
                </span>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small text-muted">
                            <tr>
                                <th class="ps-4">Pengguna</th>
                                <th>Role</th>
                                <th>Status Admin</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar-circle" style="width: 36px; height: 36px;">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">{{ $user->name }}</div>
                                                <div class="small text-muted">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $user->is_admin ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-primary-subtle text-primary border border-primary-subtle' }} rounded-pill px-2 py-1 small">
                                            {{ $user->is_admin ? 'Administrator' : 'Team Member' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($user->is_admin)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Ya</span>
                                        @else
                                            <span class="badge bg-light text-secondary border px-2 py-1">Tidak</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        @if($user->id !== auth()->id())
                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Hapus akun {{ addslashes($user->name) }} secara permanen?');" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Pengguna">
                                                    <i class="bi bi-trash3 me-1"></i> Hapus
                                                </button>
                                            </form>
                                        @else
                                            <span class="badge bg-secondary-subtle text-muted">Akun Anda</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
