@extends('layouts.master')

@section('title', 'Lapor Kondisi Barang - InApp Inventory Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="fs-3 mb-1">Laporkan Kondisi Barang</h1>
                <p class="text-muted mb-0">Laporkan perubahan kondisi fisik barang inventaris laboratorium.</p>
            </div>
            <a href="{{ route('inventaris.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Item Info Card -->
    <div class="col-12 col-lg-4 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-light fw-bold py-3">
                Informasi Barang
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block">Kode Inventaris</small>
                    <span class="fw-bold fs-5 text-dark">{{ $inventaris['kode_inventaris'] ?? '-' }}</span>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Nama Barang</small>
                    <span class="fw-semibold text-dark">{{ $inventaris['barang']['nama_barang'] ?? 'Tidak Diketahui' }}</span>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Ruangan</small>
                    <span class="badge bg-light text-dark border">{{ $inventaris['ruangan']['nama_ruangan'] ?? 'Tanpa Ruangan' }}</span>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Kondisi Saat Ini</small>
                    <span class="badge bg-info text-dark">{{ ucfirst($inventaris['kondisi'] ?? 'Baik') }}</span>
                </div>
                @if($inventaris['foto_barang'])
                    <div>
                        <small class="text-muted d-block mb-1">Foto Saat Ini</small>
                        <img src="/{{ $inventaris['foto_barang'] }}" alt="Current Photo" class="img-fluid rounded border shadow-xs" style="max-height: 180px; object-fit: contain;">
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Upload Form Card -->
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-light fw-bold py-3">
                Form Laporan Kondisi Baru
            </div>
            <div class="card-body p-4">
                <form action="{{ route('inventaris.upload-kondisi.store', $inventaris['id']) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- New Description -->
                    <div class="mb-4">
                        <label for="kondisi_pending" class="form-label fw-bold text-dark">Keterangan Kondisi Baru <span class="text-danger">*</span></label>
                        <textarea name="kondisi_pending" id="kondisi_pending" rows="4" class="form-control @error('kondisi_pending') is-invalid @enderror" placeholder="Tuliskan keterangan detail mengenai kondisi barang saat ini (misal: Rusak pada layar sentuh, retak di sisi samping, dll.)" required>{{ old('kondisi_pending') }}</textarea>
                        @error('kondisi_pending')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- New Photo -->
                    <div class="mb-4">
                        <label for="foto_pending" class="form-label fw-bold text-dark">Foto Kondisi Terbaru <span class="text-danger">*</span></label>
                        <input type="file" name="foto_pending" id="foto_pending" class="form-control @error('foto_pending') is-invalid @enderror" accept="image/*" required>
                        <small class="text-muted d-block mt-1">Harap unggah foto bukti kondisi fisik barang. Format: jpeg, png, jpg, gif. Maksimal 2MB.</small>
                        @error('foto_pending')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-warning px-4 fw-bold">
                            <i class="ti ti-upload me-1"></i> Unggah Laporan
                        </button>
                        <a href="{{ route('inventaris.index') }}" class="btn btn-light px-4 ms-2">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
