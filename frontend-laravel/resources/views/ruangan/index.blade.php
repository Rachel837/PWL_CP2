@extends('layouts.master')

@section('title', 'Daftar Ruangan - InApp Inventory Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fs-3 mb-0">Daftar Ruangan</h1>
            <a href="{{ route('ruangan.create') }}" class="btn btn-primary">
                <i class="ti ti-plus me-1"></i> Tambah Ruangan
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
                                <th class="px-4 py-3 text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kode Ruangan</th>
                                <th class="px-4 py-3 text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama Ruangan</th>
                                <th class="px-4 py-3 text-end text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ruangan as $r)
                            <tr>
                                <td class="px-4 py-3 text-sm">
                                    <span class="fw-semibold">{{ $r['kode_ruangan'] }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <h6 class="mb-0 text-sm">{{ $r['nama_ruangan'] }}</h6>
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <a href="{{ route('ruangan.edit', $r['id']) }}" class="btn btn-sm btn-outline-info me-1">Edit</a>
                                    <form action="{{ route('ruangan.destroy', $r['id']) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus ruangan ini?')">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach

                            @if(count($ruangan) === 0)
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">Belum ada data ruangan.</td>
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
