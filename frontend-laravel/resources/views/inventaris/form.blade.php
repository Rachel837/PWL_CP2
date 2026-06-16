@extends('layouts.master')

@section('title', isset($inventaris) ? 'Edit Inventaris - InApp Inventory Dashboard' : 'Tambah Inventaris - InApp Inventory Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fs-3 mb-0">{{ isset($inventaris) ? 'Edit Inventaris' : 'Tambah Inventaris Baru' }}</h1>
            <a href="{{ route('inventaris.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form action="{{ isset($inventaris) ? route('inventaris.update', $inventaris['id']) : route('inventaris.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if(isset($inventaris))
                        @method('PUT')
                    @endif

                    <div class="row">
                        <!-- Barang -->
                        <div class="col-md-6 mb-3">
                            <label for="barang_id" class="form-label fw-semibold">Pilih Barang (Katalog) <span class="text-danger">*</span></label>
                            <select name="barang_id" id="barang_id" class="form-select" required>
                                <option value="">-- Pilih Barang --</option>
                                @foreach($barang as $b)
                                    <option value="{{ $b['id'] }}" {{ old('barang_id', $inventaris['barang_id'] ?? '') == $b['id'] ? 'selected' : '' }}>
                                        {{ $b['nama_barang'] }} ({{ $b['satuan'] }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Ruangan -->
                        <div class="col-md-6 mb-3">
                            <label for="ruangan_id" class="form-label fw-semibold">Pilih Ruangan</label>
                            <select name="ruangan_id" id="ruangan_id" class="form-select">
                                <option value="">-- Tanpa Ruangan (Gudang/Lainnya) --</option>
                                @foreach($ruangan as $r)
                                    <option value="{{ $r['id'] }}" {{ old('ruangan_id', $inventaris['ruangan_id'] ?? '') == $r['id'] ? 'selected' : '' }}>
                                        {{ $r['nama_ruangan'] }} ({{ $r['kode_ruangan'] }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Kode Inventaris -->
                        <div class="col-md-6 mb-3">
                            <label for="kode_inventaris" class="form-label fw-semibold">Kode Inventaris <span class="text-danger">*</span></label>
                            <input type="text" name="kode_inventaris" id="kode_inventaris" class="form-control" placeholder="Contoh: INV/LAB-KOM/2026/001" value="{{ old('kode_inventaris', $inventaris['kode_inventaris'] ?? '') }}" required>
                        </div>

                        <!-- QR Code -->
                        <div class="col-md-6 mb-3">
                            <label for="qr_code" class="form-label fw-semibold">QR Code / Serial Number</label>
                            <input type="text" name="qr_code" id="qr_code" class="form-control" placeholder="Masukkan serial number atau string QR" value="{{ old('qr_code', $inventaris['qr_code'] ?? '') }}">
                        </div>
                    </div>

                    <div class="row">
                        <!-- Kondisi -->
                        <div class="col-md-6 mb-3">
                            <label for="kondisi" class="form-label fw-semibold">Kondisi Barang <span class="text-danger">*</span></label>
                            <input type="text" name="kondisi" id="kondisi" class="form-control" placeholder="Contoh: Baik, Rusak Ringan, Rusak Berat" value="{{ old('kondisi', $inventaris['kondisi'] ?? 'Baik') }}" required>
                        </div>

                        <!-- Tanggal Masuk -->
                        <div class="col-md-6 mb-3">
                            <label for="tanggal_masuk" class="form-label fw-semibold">Tanggal Masuk <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_masuk" id="tanggal_masuk" class="form-control" value="{{ old('tanggal_masuk', isset($inventaris['tanggal_masuk']) ? date('Y-m-d', strtotime($inventaris['tanggal_masuk'])) : date('Y-m-d')) }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Status Barang -->
                        <div class="col-md-6 mb-3">
                            <label for="status_barang" class="form-label fw-semibold">Status Operasional <span class="text-danger">*</span></label>
                            <select name="status_barang" id="status_barang" class="form-select" required>
                                <option value="aktif" {{ old('status_barang', $inventaris['status_barang'] ?? '') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="nonaktif" {{ old('status_barang', $inventaris['status_barang'] ?? '') == 'nonaktif' ? 'selected' : '' }}>Nonaktif (Diarsipkan)</option>
                            </select>
                        </div>

                        <!-- Status Inventaris -->
                        <div class="col-md-6 mb-3">
                            <label for="status_inventaris" class="form-label fw-semibold">Status Keberadaan <span class="text-danger">*</span></label>
                            <select name="status_inventaris" id="status_inventaris" class="form-select" required>
                                <option value="tersedia" {{ old('status_inventaris', $inventaris['status_inventaris'] ?? '') == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                                <option value="dipinjam" {{ old('status_inventaris', $inventaris['status_inventaris'] ?? '') == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                                <option value="rusak" {{ old('status_inventaris', $inventaris['status_inventaris'] ?? '') == 'rusak' ? 'selected' : '' }}>Rusak</option>
                            </select>
                        </div>
                    </div>

                    <!-- Foto Barang -->
                    <div class="mb-3">
                        <label for="foto_barang" class="form-label fw-semibold">Foto Barang</label>
                        @if(isset($inventaris) && $inventaris['foto_barang'])
                            <div class="mb-2">
                                <img src="/{{ $inventaris['foto_barang'] }}" alt="Current Photo" class="img-thumbnail" style="max-height: 150px; object-fit: contain;">
                                <small class="text-muted d-block mt-1">Foto saat ini</small>
                            </div>
                        @endif
                        <input type="file" name="foto_barang" id="foto_barang" class="form-control" accept="image/*">
                        <small class="text-muted">Format file yang diperbolehkan: jpeg, png, jpg, gif. Max 2MB.</small>
                    </div>

                    <div class="mt-4 pt-2 border-top">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="ti ti-device-floppy me-1"></i> Simpan
                        </button>
                        <a href="{{ route('inventaris.index') }}" class="btn btn-light px-4 ms-2">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
