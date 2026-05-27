@extends('layouts.master')

@section('title', isset($user) ? 'Edit Pengguna - InApp Inventory Dashboard' : 'Tambah Pengguna - InApp Inventory Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fs-3 mb-0">{{ isset($user) ? 'Edit Pengguna' : 'Tambah Pengguna' }}</h1>
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 col-lg-8">
        <div class="card">
            <div class="card-body p-4">
                <form action="{{ isset($user) ? route('users.update', $user['id']) : route('users.store') }}" method="POST">
                    @csrf
                    @if(isset($user))
                        @method('PUT')
                    @endif

                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama</label>
                        <input type="text" name="nama" id="nama" class="form-control" value="{{ old('nama', $user['nama'] ?? '') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user['email'] ?? '') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password {{ isset($user) ? '(Kosongkan jika tidak ingin mengubah)' : '' }}</label>
                        <input type="password" name="password" id="password" class="form-control" {{ isset($user) ? '' : 'required' }}>
                    </div>

                    <div class="mb-3">
                        <label for="roles_id" class="form-label">Role</label>
                        <select name="roles_id" id="roles_id" class="form-select" required>
                            <option value="">Pilih Role...</option>
                            <option value="1" {{ old('roles_id', $user['roles_id'] ?? '') == 1 ? 'selected' : '' }}>Administrator</option>
                            <option value="2" {{ old('roles_id', $user['roles_id'] ?? '') == 2 ? 'selected' : '' }}>Kepala Laboratorium</option>
                            <option value="3" {{ old('roles_id', $user['roles_id'] ?? '') == 3 ? 'selected' : '' }}>Ketua Program Studi</option>
                            <option value="4" {{ old('roles_id', $user['roles_id'] ?? '') == 4 ? 'selected' : '' }}>Staf Administrasi</option>
                            <option value="5" {{ old('roles_id', $user['roles_id'] ?? '') == 5 ? 'selected' : '' }}>Staf Laboratorium</option>
                        </select>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
