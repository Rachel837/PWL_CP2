@extends('layouts.master')

@section('title', isset($stok) ? 'Edit Stok BHP - InApp Inventory Dashboard' : 'Tambah Stok BHP - InApp Inventory Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="fs-3 mb-1">{{ isset($stok) ? 'Edit Stok BHP' : 'Tambah Stok BHP' }}</h1>
                <p class="text-muted mb-0 text-sm">
                    {{ isset($stok) ? 'Perbarui jumlah stok dan batas minimum peringatan untuk barang terpilih.' : 'Daftarkan barang habis pakai (BHP) baru ke sistem pemantauan stok.' }}
                </p>
            </div>
            <a href="{{ route('stok-bhp.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form action="{{ isset($stok) ? route('stok-bhp.update', $stok['id']) : route('stok-bhp.store') }}" method="POST">
                    @csrf
                    @if(isset($stok))
                        @method('PUT')
                    @endif

                    @if(isset($stok))
                        <!-- Tampilan Hanya Edit (Read-Only Detail Barang) -->
                        <div class="mb-4 p-3 bg-light rounded">
                            <span class="text-xs text-uppercase text-secondary d-block font-weight-bold mb-1">Barang Habis Pakai</span>
                            <h5 class="mb-1 text-dark fw-bold">{{ $stok['barang']['nama_barang'] ?? '' }}</h5>
                            <p class="text-xs text-muted mb-0">Spesifikasi: {{ $stok['barang']['spesifikasi'] ?? '-' }}</p>
                            <p class="text-xs text-muted mb-0">Satuan Barang: <span class="badge bg-secondary">{{ $stok['barang']['satuan'] ?? 'Unit' }}</span></p>
                        </div>
                    @else
                        <!-- Dropdown Pilihan Barang BHP saat Create -->
                        <div class="mb-3">
                            <label for="barang_id" class="form-label font-weight-bold">Pilih Barang BHP <span class="text-danger">*</span></label>
                            <select name="barang_id" id="barang_id" class="form-select" required>
                                <option value="" disabled selected>-- Pilih Barang BHP yang Tersedia --</option>
                                @foreach($availableItems as $item)
                                    <option value="{{ $item['id'] }}" {{ old('barang_id') == $item['id'] ? 'selected' : '' }}>
                                        {{ $item['nama_barang'] }} ({{ $item['spesifikasi'] ?? 'No Spec' }}) - Satuan: {{ $item['satuan'] }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text text-muted text-xs mt-1">Barang yang muncul adalah barang bertipe BHP yang belum memiliki catatan stok.</div>
                            @if(count($availableItems) === 0)
                                <div class="alert alert-warning mt-2 text-xs py-2">
                                    <i class="ti ti-info-circle me-1"></i> Semua barang BHP sudah memiliki catatan stok. Anda hanya dapat memperbarui stok yang ada.
                                </div>
                            @endif
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="jumlah_stok" class="form-label">Jumlah Stok <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="jumlah_stok" id="jumlah_stok" min="0" class="form-control" value="{{ old('jumlah_stok', $stok['jumlah_stok'] ?? '0') }}" required placeholder="Kuantitas stok saat ini">
                                <span class="input-group-text bg-light text-muted">{{ isset($stok) ? ($stok['barang']['satuan'] ?? 'Unit') : 'Satuan' }}</span>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="minimal_stok" class="form-label">Minimal Stok <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="minimal_stok" id="minimal_stok" min="0" class="form-control" value="{{ old('minimal_stok', $stok['minimal_stok'] ?? '0') }}" required placeholder="Batas minimum stok">
                                <span class="input-group-text bg-light text-muted">{{ isset($stok) ? ($stok['barang']['satuan'] ?? 'Unit') : 'Satuan' }}</span>
                            </div>
                            <div class="form-text text-muted text-xs mt-1">Sistem akan memberi tanda peringatan jika stok berada di bawah kuantitas ini.</div>
                        </div>
                    </div>

                    <div class="mt-4 pt-2 border-top">
                        <button type="submit" class="btn btn-primary px-4" {{ !isset($stok) && count($availableItems) === 0 ? 'disabled' : '' }}>
                            <i class="ti ti-device-floppy me-1"></i> Simpan Data
                        </button>
                        <a href="{{ route('stok-bhp.index') }}" class="btn btn-outline-secondary px-3 ms-2">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
