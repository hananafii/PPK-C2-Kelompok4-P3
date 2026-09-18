@extends('layouts.app')

@section('title', 'Tambah Pengguna Baru')
@section('page-title', 'Tambah Pengguna')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm border-0 p-4">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="avatar-circle bg-primary-subtle text-primary" style="width: 44px; height: 44px; font-size: 1.2rem;">
                    <i class="bi bi-person-plus-fill"></i>
                </div>
                <div>
                    <h5 class="fw-bold text-dark mb-0">Tambah Pengguna Baru</h5>
                    <small class="text-muted">Buat akun untuk anggota tim atau admin</small>
                </div>
            </div>

            <form action="{{ route('users.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-secondary" for="name">Nama Lengkap</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-person"></i></span>
                        <input id="name" type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Masukkan nama..." required autofocus>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-secondary" for="email">Alamat Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-envelope"></i></span>
                        <input id="email" type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="nama@email.com" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-semibold text-secondary" for="password">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-lock"></i></span>
                        <input id="password" type="password" name="password" class="form-control" placeholder="Minimal 8 karakter..." required>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                    <a href="{{ route('users.index') }}" class="btn btn-sm btn-light border text-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-sm btn-jara-primary px-3">
                        <i class="bi bi-check2 me-1"></i> Simpan Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection