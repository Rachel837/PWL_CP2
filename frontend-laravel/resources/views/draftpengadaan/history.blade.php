@extends('layouts.master')

@section('title', 'History Pengadaan Barang - InApp Inventory Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fs-3 mb-0">History Pengadaan Barang</h1>
        </div>
    </div>
</div>

<!-- Draft List -->
@if(count($historyDrafts) > 0)
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3">Tahun</th>
                            <th class="px-4 py-3">Total Barang</th>
                            <th class="px-4 py-3">Total Estimasi</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($historyDrafts as $draft)
                            @php
                                $statusClass = match($draft['status'] ?? 'draft') {
                                    'approved', 'finalized', 'disetujui' => 'bg-success',
                                    'submitted', 'reviewed', 'diajukan' => 'bg-info text-dark',
                                    'rejected', 'ditolak' => 'bg-danger',
                                    'locked' => 'bg-dark',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <tr>
                                <td class="px-4 py-3 fw-bold">{{ $draft['tahun'] }}</td>
                                <td class="px-4 py-3">{{ count($draft['details'] ?? []) }} item</td>
                                <td class="px-4 py-3 text-success fw-semibold">
                                    Rp {{ number_format(array_sum(array_map(function($detail) { 
                                        return ($detail['harga_estimasi'] ?? 0) * ($detail['jumlah'] ?? 0); 
                                    }, $draft['details'] ?? [])), 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="badge {{ $statusClass }}">
                                        {{ ucfirst($draft['status'] ?? 'draft') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('draft-pengadaan.show', $draft['id']) }}" class="btn btn-sm btn-outline-success" title="Lihat Detail">
                                            <i class="ti ti-eye"></i> Lihat
                                        </a>
                                        @if(Session::has('user') && Session::get('user')['role'] === 'kepala laboratorium')
                                            @if($draft['status'] !== 'locked' && $draft['status'] !== 'disetujui' && $draft['status'] !== 'diajukan' && $draft['status'] !== 'ditolak')
                                                <a href="{{ route('draft-pengadaan.edit', $draft['id']) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                                    <i class="ti ti-edit"></i>
                                                </a>
                                            @else
                                                <button type="button" class="btn btn-sm btn-outline-secondary" disabled title="Tidak bisa diedit">
                                                    <i class="ti ti-lock"></i>
                                                </button>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@else
    <div class="row">
        <div class="col-12">
            <div class="card text-center p-5 border-dashed">
                <div class="card-body">
                    <i class="ti ti-history fs-1 text-muted mb-3 d-block"></i>
                    <h4 class="fw-bold text-dark mb-2">Belum Ada History Pengadaan</h4>
                    <p class="text-muted mb-4">Anda belum pernah mengajukan draf pengadaan barang.</p>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
