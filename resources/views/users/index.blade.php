@extends('layouts.app')

@section('title', 'Manajemen Pengguna')
@section('page-title', 'User Administration')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-bottom p-4 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
        <div>
            <h5 class="fw-bold text-dark mb-0">
                <i class="bi bi-people-fill text-primary me-2"></i> Manajemen Pengguna
            </h5>
            <small class="text-muted">Kelola akun dan hak akses pengguna sistem JARA</small>
        </div>
        <a href="{{ route('users.create') }}" class="btn btn-sm btn-jara-primary d-inline-flex align-items-center gap-1">
            <i class="bi bi-person-plus-fill"></i> Tambah Pengguna
        </a>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-muted">
                    <tr>
                        <th class="ps-4" width="8%">ID</th>
                        <th>Nama Lengkap</th>
                        <th>Email</th>
                        <th width="15%" class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td class="ps-4 text-muted small">#{{ $user->id }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-circle" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <span class="fw-bold text-dark">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="text-muted">{{ $user->email }}</td>
                            <td class="text-center pe-4">
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ addslashes($user->name) }}?');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Akun">
                                        <i class="bi bi-trash3 me-1"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="bi bi-people fs-2 d-block mb-2"></i>
                                Belum ada data pengguna.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection