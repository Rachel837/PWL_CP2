@extends('layouts.master')

@section('title', 'Detail Draf Pengadaan - InApp Inventory Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="fs-3 mb-0">Detail Draf Pengadaan Tahun {{ $draftPengadaan['tahun'] }}</h1>
            <a href="{{ route('draft-pengadaan.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>
</div>

<!-- Header Info Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <small class="text-muted d-block mb-1">Status</small>
                @php
                    $statusClass = match($draftPengadaan['status'] ?? 'draft') {
                        'disetujui' => 'bg-success text-white',
                        'diajukan' => 'bg-info text-dark',
                        'ditolak' => 'bg-danger text-white',
                        default => 'bg-secondary text-white'
                    };
                @endphp
                <span class="badge {{ $statusClass }} fs-6 px-3 py-1 mt-1">
                    {{ ucfirst($draftPengadaan['status']) }}
                </span>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <small class="text-muted d-block mb-1">Total Barang</small>
                <h3 class="fw-bold text-primary mb-0 mt-1">
                    {{ count($draftPengadaan['details'] ?? []) }} <span class="fs-6 fw-normal text-muted">item</span>
                </h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <small class="text-muted d-block mb-1">Total Estimasi</small>
                <h3 class="fw-bold text-success mb-0 mt-1">
                    Rp {{ number_format(array_sum(array_map(function($detail) { 
                        return ($detail['harga_estimasi'] ?? 0) * ($detail['jumlah'] ?? 0); 
                    }, $draftPengadaan['details'] ?? [])), 0, ',', '.') }}
                </h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <small class="text-muted d-block mb-1">Dibuat Oleh</small>
                <h5 class="fw-bold text-dark mb-0 mt-1">{{ $draftPengadaan['pengguna']['nama'] ?? '-' }}</h5>
                <small class="text-muted">{{ $draftPengadaan['pengguna']['email'] ?? '-' }}</small>
            </div>
        </div>
    </div>
</div>

@if($draftPengadaan['catatan'])
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info border-0 shadow-sm mb-0">
                <h6 class="fw-bold"><i class="ti ti-info-circle me-1"></i> Catatan Pembuat:</h6>
                <p class="mb-0 small fst-italic">{{ $draftPengadaan['catatan'] }}</p>
            </div>
        </div>
    </div>
@endif

<!-- Items Table -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h5 class="fw-bold text-dark mb-3">Daftar Barang yang Diusulkan</h5>
                
                @if(count($draftPengadaan['details'] ?? []) > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-3 py-2 text-center" style="width: 50px;">No.</th>
                                    <th class="px-3 py-2">Nama Barang</th>
                                    <th class="px-3 py-2">Kategori</th>
                                    <th class="px-3 py-2">Satuan</th>
                                    <th class="px-3 py-2 text-center">Jumlah</th>
                                    <th class="px-3 py-2 text-end">Harga Estimasi</th>
                                    <th class="px-3 py-2 text-end">Subtotal</th>
                                    <th class="px-3 py-2 text-center">Status Approval</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $totalHarga = 0; @endphp
                                @foreach($draftPengadaan['details'] as $index => $detail)
                                    @php 
                                        $subtotal = ($detail['harga_estimasi'] ?? 0) * ($detail['jumlah'] ?? 0);
                                        $totalHarga += $subtotal;
                                    @endphp
                                    <tr>
                                        <td class="px-3 py-3 text-center text-muted small">{{ $index + 1 }}</td>
                                        <td class="px-3 py-3">
                                            @if($detail['link_pembelian'])
                                                <a href="{{ $detail['link_pembelian'] }}" target="_blank" class="text-primary text-decoration-none fw-semibold">
                                                    {{ $detail['barang']['nama_barang'] ?? '-' }} <i class="ti ti-external-link small"></i>
                                                </a>
                                            @else
                                                <span class="fw-semibold">{{ $detail['barang']['nama_barang'] ?? '-' }}</span>
                                            @endif
                                            @if($detail['inventaris_lama'] ?? null)
                                                <div class="mt-1">
                                                    <span class="badge bg-warning text-dark text-xxs">
                                                        Ganti: {{ $detail['inventaris_lama']['kode_inventaris'] ?? '-' }} 
                                                        ({{ $detail['inventaris_lama']['kondisi'] ?? '-' }})
                                                    </span>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-3 py-3">
                                            <span class="badge bg-secondary">
                                                {{ $detail['barang']['kategori']['nama_kategori'] ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-3 text-muted small">{{ $detail['barang']['satuan'] ?? '-' }}</td>
                                        <td class="px-3 py-3 text-center fw-medium">{{ $detail['jumlah'] }}</td>
                                        <td class="px-3 py-3 text-end text-muted">Rp {{ number_format($detail['harga_estimasi'] ?? 0, 0, ',', '.') }}</td>
                                        <td class="px-3 py-3 text-end fw-semibold text-dark">
                                            Rp {{ number_format($subtotal, 0, ',', '.') }}
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            @php
                                                $appClass = match($detail['status_approval'] ?? 'pending') {
                                                    'approved', 'disetujui' => 'bg-success text-white',
                                                    'rejected', 'ditolak' => 'bg-danger text-white',
                                                    default => 'bg-warning text-dark'
                                                };
                                            @endphp
                                            <span class="badge {{ $appClass }}">
                                                {{ ucfirst($detail['status_approval'] ?? 'pending') }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <td colspan="6" class="px-3 py-3 text-end">Total Anggaran:</td>
                                    <td class="px-3 py-3 text-end text-success fs-5">Rp {{ number_format($totalHarga, 0, ',', '.') }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-4 d-flex gap-2">
                        @if($draftPengadaan['status'] === 'draft')
                            <a 
                                href="{{ route('draft-pengadaan.edit', $draftPengadaan['id']) }}"
                                class="btn btn-primary px-4"
                            >
                                <i class="ti ti-edit me-1"></i> Edit Draf
                            </a>
                            <form action="{{ route('draft-pengadaan.submit', $draftPengadaan['id']) }}" method="POST" class="d-inline mb-0">
                                @csrf
                                <button 
                                    type="submit"
                                    class="btn btn-success px-4"
                                >
                                    <i class="ti ti-send me-1"></i> Ajukan untuk Approval
                                </button>
                            </form>
                        @endif
                        
                        <form action="{{ route('draft-pengadaan.destroy', $draftPengadaan['id']) }}" method="POST" class="d-inline mb-0">
                            @csrf
                            @method('DELETE')
                            <button 
                                type="submit"
                                onclick="return confirm('Yakin ingin menghapus draf ini?')"
                                class="btn btn-outline-danger px-4"
                            >
                                <i class="ti ti-trash me-1"></i> Hapus Draf
                            </button>
                        </form>
                    </div>
                @else
                    <div class="text-center py-5 bg-light rounded border-dashed">
                        <i class="ti ti-package fs-1 text-muted mb-2 d-block"></i>
                        <p class="text-muted mb-3">Belum ada barang yang ditambahkan.</p>
                        <a 
                            href="{{ route('draft-pengadaan.edit', $draftPengadaan['id']) }}"
                            class="btn btn-primary btn-sm"
                        >
                            Tambah Barang
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
