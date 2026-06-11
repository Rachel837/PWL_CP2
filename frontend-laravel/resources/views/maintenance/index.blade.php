@extends('layouts.master')

@section('title', 'Log Maintenance - InApp Inventory Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="fs-3 mb-1">Riwayat Log Maintenance</h1>
                <p class="text-muted mb-0 text-sm">Lihat aktivitas pemeliharaan barang inventaris laboratorium dan penggunaan bahan habis pakai (BHP).</p>
            </div>
            <a href="{{ route('maintenance.create') }}" class="btn btn-primary">
                <i class="ti ti-plus me-1"></i> Catat Maintenance Baru
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
                                <th class="px-4 py-3 text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tanggal</th>
                                <th class="px-4 py-3 text-secondary text-xxs font-weight-bolder opacity-7">Barang Inventaris</th>
                                <th class="px-4 py-3 text-secondary text-xxs font-weight-bolder opacity-7">Kondisi (Sblm -> Ssdh)</th>
                                <th class="px-4 py-3 text-secondary text-xxs font-weight-bolder opacity-7">Petugas</th>
                                <th class="px-4 py-3 text-secondary text-xxs font-weight-bolder opacity-7">Tindakan</th>
                                <th class="px-4 py-3 text-end text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($maintenances as $m)
                            <tr>
                                <td class="px-4 py-3 text-sm text-dark fw-semibold">
                                    {{ date('d M Y', strtotime($m['tanggal_maintenance'])) }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-dark">{{ $m['inventaris']['barang']['nama_barang'] ?? 'Tidak Diketahui' }}</span>
                                        <span class="text-xs text-secondary font-monospace">{{ $m['inventaris']['kode_inventaris'] ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-warning text-dark text-xs px-2 py-1">{{ $m['kondisi_sebelum'] }}</span>
                                        <i class="ti ti-arrow-narrow-right mx-2 text-muted"></i>
                                        <span class="badge bg-success text-xs px-2 py-1">{{ $m['kondisi_sesudah'] }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <div class="fw-semibold text-dark">{{ $m['user']['nama'] ?? 'Tidak Diketahui' }}</div>
                                    <div class="text-xs text-muted">{{ $m['user']['email'] ?? '' }}</div>
                                </td>
                                <td class="px-4 py-3 text-sm text-muted">
                                    {{ \Illuminate\Support\Str::limit($m['tindakan'] ?: '-', 40) }}
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <a href="{{ route('maintenance.show', $m['id']) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="ti ti-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                            @endforeach

                            @if(count($maintenances) === 0)
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="ti ti-history fs-1 mb-2 d-block"></i>
                                    Belum ada riwayat aktivitas log maintenance.
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
