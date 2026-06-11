@extends('layouts.master')

@section('title', isset($ruangan) ? 'Edit Ruangan - InApp Inventory Dashboard' : 'Tambah Ruangan - InApp Inventory Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fs-3 mb-0">{{ isset($ruangan) ? 'Edit Ruangan' : 'Tambah Ruangan' }}</h1>
            <a href="{{ route('ruangan.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 col-lg-8">
        <div class="card">
            <div class="card-body p-4">
                <form action="{{ isset($ruangan) ? route('ruangan.update', $ruangan['id']) : route('ruangan.store') }}" method="POST">
                    @csrf
                    @if(isset($ruangan))
                        @method('PUT')
                    @endif

                    <div class="mb-3">
                        <label for="kode_ruangan" class="form-label">Kode Ruangan</label>
                        <input type="text" name="kode_ruangan" id="kode_ruangan" class="form-control" value="{{ old('kode_ruangan', $ruangan['kode_ruangan'] ?? '') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="nama_ruangan" class="form-label">Nama Ruangan</label>
                        <input type="text" name="nama_ruangan" id="nama_ruangan" class="form-control" value="{{ old('nama_ruangan', $ruangan['nama_ruangan'] ?? '') }}" required>
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
