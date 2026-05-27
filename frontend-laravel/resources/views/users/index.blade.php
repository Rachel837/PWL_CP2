@extends('layouts.master')

@section('title', 'Daftar Pengguna - InApp Inventory Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fs-3 mb-0">Daftar Pengguna</h1>
            <a href="{{ route('users.create') }}" class="btn btn-primary">
                <i class="ti ti-plus me-1"></i> Tambah Pengguna
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3 text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama</th>
                                <th class="px-4 py-3 text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Email</th>
                                <th class="px-4 py-3 text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Role</th>
                                <th class="px-4 py-3 text-end text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td class="px-4 py-3">
                                    <h6 class="mb-0 text-sm">{{ $user['nama'] }}</h6>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $user['email'] }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="badge bg-secondary">{{ $user['role']['nama'] ?? '-' }}</span>
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <a href="{{ route('users.edit', $user['id']) }}" class="btn btn-sm btn-outline-info me-1">Edit</a>
                                    <form action="{{ route('users.destroy', $user['id']) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                            
                            @if(count($users) === 0)
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Belum ada data pengguna.</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
