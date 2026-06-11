@extends('layouts.master')

@section('title', 'Kelola Stok BHP - InApp Inventory Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="fs-3 mb-1">Kelola Stok BHP</h1>
                <p class="text-muted mb-0 text-sm">Kelola kuantitas barang habis pakai laboratorium dan atur batas minimum stok.</p>
            </div>
            <a href="{{ route('stok-bhp.create') }}" class="btn btn-primary">
                <i class="ti ti-plus me-1"></i> Tambah Stok BHP
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3 text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama Barang</th>
                                <th class="px-4 py-3 text-secondary text-xxs font-weight-bolder opacity-7">Spesifikasi</th>
                                <th class="px-4 py-3 text-center text-secondary text-xxs font-weight-bolder opacity-7">Kuantitas Stok</th>
                                <th class="px-4 py-3 text-center text-secondary text-xxs font-weight-bolder opacity-7">Min. Stok</th>
                                <th class="px-4 py-3 text-center text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                <th class="px-4 py-3 text-end text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stokBhp as $s)
                            @php
                                $stockVal = intval($s['jumlah_stok']);
                                $minVal = intval($s['minimal_stok']);
                                $isAlert = $stockVal <= $minVal;
                                $isOutOfStock = $stockVal === 0;
                            @endphp
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-dark">{{ $s['barang']['nama_barang'] ?? 'Tidak Diketahui' }}</span>
                                        <span class="text-xs text-muted">Satuan: {{ $s['barang']['satuan'] ?? 'Unit' }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $s['barang']['spesifikasi'] ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-center fw-semibold text-dark text-sm">
                                    {{ $s['jumlah_stok'] }} {{ $s['barang']['satuan'] ?? '' }}
                                </td>
                                <td class="px-4 py-3 text-center text-muted text-sm">
                                    {{ $s['minimal_stok'] }} {{ $s['barang']['satuan'] ?? '' }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($isOutOfStock)
                                        <span class="badge bg-danger rounded-pill px-3 py-2 text-xs">Habis</span>
                                    @elseif($isAlert)
                                        <span class="badge bg-warning rounded-pill px-3 py-2 text-xs text-dark">Stok Rendah</span>
                                    @else
                                        <span class="badge bg-success rounded-pill px-3 py-2 text-xs">Aman</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <a href="{{ route('stok-bhp.edit', $s['id']) }}" class="btn btn-sm btn-outline-info me-1">
                                        <i class="ti ti-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('stok-bhp.destroy', $s['id']) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus pencatatan stok BHP ini?')">
                                            <i class="ti ti-trash"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach

                            @if(count($stokBhp) === 0)
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="ti ti-box fs-1 mb-2 d-block"></i>
                                    Belum ada data stok BHP yang didaftarkan.
                                </td>
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
