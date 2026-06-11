@extends('layouts.master')

@section('title', 'Detail Log Maintenance - InApp Inventory Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="fs-3 mb-1">Detail Log Maintenance</h1>
                <p class="text-muted mb-0 text-sm">Informasi lengkap mengenai tindakan pemeliharaan dan penggunaan bahan habis pakai (BHP).</p>
            </div>
            <a href="{{ route('maintenance.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Kolom Utama: Informasi Perbaikan -->
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-light py-3 border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-dark fw-bold">Detail Tindakan Pemeliharaan</h5>
                    <span class="badge bg-secondary px-3 py-2 font-monospace">{{ date('d M Y', strtotime($maintenance['tanggal_maintenance'])) }}</span>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <span class="text-xs text-secondary text-uppercase d-block font-weight-bold mb-1">Barang Inventaris</span>
                        <h6 class="text-dark fw-bold mb-1">{{ $maintenance['inventaris']['barang']['nama_barang'] ?? 'Tidak Diketahui' }}</h6>
                        <span class="text-xs text-muted font-monospace d-block">{{ $maintenance['inventaris']['kode_inventaris'] ?? '-' }}</span>
                        <span class="text-xs text-muted d-block">Spesifikasi: {{ $maintenance['inventaris']['barang']['spesifikasi'] ?? '-' }}</span>
                    </div>
                    
                    <div class="col-md-6">
                        <span class="text-xs text-secondary text-uppercase d-block font-weight-bold mb-1">Petugas Pelaksana</span>
                        <h6 class="text-dark fw-bold mb-1">{{ $maintenance['user']['nama'] ?? 'Tidak Diketahui' }}</h6>
                        <span class="text-xs text-muted d-block">{{ $maintenance['user']['email'] ?? '' }}</span>
                    </div>
                </div>

                <div class="row p-3 bg-light rounded mb-4">
                    <div class="col-6 text-center border-end">
                        <span class="text-xs text-secondary text-uppercase d-block mb-1">Kondisi Sebelum</span>
                        <span class="badge bg-warning text-dark px-3 py-2 text-sm">{{ strtoupper($maintenance['kondisi_sebelum']) }}</span>
                    </div>
                    <div class="col-6 text-center">
                        <span class="text-xs text-secondary text-uppercase d-block mb-1">Kondisi Sesudah</span>
                        <span class="badge bg-success px-3 py-2 text-sm">{{ strtoupper($maintenance['kondisi_sesudah']) }}</span>
                    </div>
                </div>

                <div class="mb-3">
                    <span class="text-xs text-secondary text-uppercase d-block font-weight-bold mb-1">Tindakan Perbaikan</span>
                    <p class="text-dark bg-light p-3 rounded text-sm mb-0 style-content" style="white-space: pre-wrap;">{{ $maintenance['tindakan'] ?: 'Tidak ada penjelasan tindakan.' }}</p>
                </div>

                <div class="mb-0">
                    <span class="text-xs text-secondary text-uppercase d-block font-weight-bold mb-1">Catatan Tambahan</span>
                    <p class="text-dark bg-light p-3 rounded text-sm mb-0 style-content" style="white-space: pre-wrap;">{{ $maintenance['catatan'] ?: '-' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Penggunaan BHP -->
    <div class="col-12 col-lg-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-light py-3 border-0">
                <h5 class="mb-0 text-dark fw-bold">Penggunaan BHP</h5>
            </div>
            <div class="card-body p-3">
                @if(isset($maintenance['usedBhp']) && count($maintenance['usedBhp']) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead>
                                <tr class="text-xs text-secondary">
                                    <th class="ps-0 py-2">Nama Barang</th>
                                    <th class="text-center py-2">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($maintenance['usedBhp'] as $item)
                                <tr>
                                    <td class="ps-0 py-2">
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold text-dark text-sm">{{ $item['barang']['nama_barang'] ?? 'Barang Habis Pakai' }}</span>
                                            <span class="text-xxs text-muted">{{ $item['barang']['spesifikasi'] ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center py-2 text-sm fw-semibold text-dark">
                                        {{ $item['jumlah_digunakan'] }} {{ $item['barang']['satuan'] ?? '' }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4 text-muted">
                        <i class="ti ti-info-circle fs-2 mb-2 d-block text-secondary"></i>
                        Tidak ada bahan habis pakai (BHP) yang digunakan dalam perbaikan ini.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
