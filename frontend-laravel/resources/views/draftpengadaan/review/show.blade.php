@extends('layouts.master')

@section('title', 'Detail Review Pengadaan - InApp Inventory Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fs-3 mb-0">Detail Review Pengadaan #{{ $draftPengadaan['id'] }}</h1>
            <a href="{{ route('draft-pengadaan.review.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white pb-0">
                <h5 class="card-title mb-0">Informasi Draf</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr>
                        <td class="text-muted w-50">Tahun Pengadaan</td>
                        <td class="fw-bold">{{ $draftPengadaan['tahun'] }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Diajukan Oleh</td>
                        <td class="fw-bold">{{ $draftPengadaan['pengguna']['nama'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tanggal Diajukan</td>
                        <td class="fw-bold">{{ \Carbon\Carbon::parse($draftPengadaan['created_at'])->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status Draf</td>
                        <td>
                            @if($draftPengadaan['status'] === 'diajukan')
                                <span class="badge bg-warning">Menunggu Review</span>
                            @elseif($draftPengadaan['status'] === 'disetujui')
                                <span class="badge bg-success">Disetujui</span>
                            @elseif($draftPengadaan['status'] === 'ditolak')
                                <span class="badge bg-danger">Ditolak</span>
                            @else
                                <span class="badge bg-secondary">{{ $draftPengadaan['status'] }}</span>
                            @endif
                        </td>
                    </tr>
                </table>

                @if($draftPengadaan['catatan'])
                <div class="mt-3">
                    <p class="text-muted mb-1 text-sm">Catatan Pengaju:</p>
                    <div class="p-3 bg-light rounded text-sm">
                        {{ $draftPengadaan['catatan'] }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-12 col-lg-8 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white pb-0 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Daftar Barang Pengadaan</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3 text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Barang</th>
                                <th class="px-4 py-3 text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Jumlah</th>
                                <th class="px-4 py-3 text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-end">Est. Harga</th>
                                <th class="px-4 py-3 text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Status</th>
                                <th class="px-4 py-3 text-end text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalEstimasi = 0; @endphp
                            @forelse($draftPengadaan['details'] ?? [] as $detail)
                            @php $totalEstimasi += ($detail['harga_estimasi'] * $detail['jumlah']); @endphp
                            <tr>
                                <td class="px-4 py-3">
                                    <h6 class="mb-0 text-sm">{{ $detail['barang']['nama_barang'] ?? '-' }}</h6>
                                    @if($detail['link_pembelian'])
                                        <a href="{{ $detail['link_pembelian'] }}" target="_blank" class="text-xs text-primary"><i class="ti ti-link"></i> Link Referensi</a>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center text-sm">
                                    {{ $detail['jumlah'] }} {{ $detail['barang']['satuan'] ?? '' }}
                                </td>
                                <td class="px-4 py-3 text-end text-sm">
                                    Rp {{ number_format($detail['harga_estimasi'], 0, ',', '.') }}<br>
                                    <small class="text-muted">Total: Rp {{ number_format($detail['harga_estimasi'] * $detail['jumlah'], 0, ',', '.') }}</small>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($detail['status_approval'] === 'disetujui')
                                        <span class="badge bg-success">Disetujui</span>
                                    @elseif($detail['status_approval'] === 'ditolak')
                                        <span class="badge bg-danger">Ditolak</span>
                                    @else
                                        <span class="badge bg-warning">Pending</span>
                                    @endif
                                    
                                    @if($detail['catatan_kaprodi'])
                                        <i class="ti ti-message-circle text-info ms-1" data-bs-toggle="tooltip" title="{{ $detail['catatan_kaprodi'] }}"></i>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-end">
                                    @if($draftPengadaan['status'] === 'diajukan')
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#reviewModal{{ $detail['id'] }}">
                                        Review
                                    </button>
                                    @else
                                    <button type="button" class="btn btn-sm btn-outline-secondary" disabled>
                                        Final
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            
                            <!-- Review Modal for Item -->
                            <div class="modal fade" id="reviewModal{{ $detail['id'] }}" tabindex="-1" aria-labelledby="reviewModalLabel{{ $detail['id'] }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="reviewModalLabel{{ $detail['id'] }}">Review Item: {{ $detail['barang']['nama_barang'] ?? '-' }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('draft-pengadaan.review.update-detail', ['id' => $draftPengadaan['id'], 'detailId' => $detail['id']]) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Keputusan Review</label>
                                                    <select name="status_approval" class="form-select" required>
                                                        <option value="pending" {{ $detail['status_approval'] === 'pending' ? 'selected' : '' }}>Pending</option>
                                                        <option value="disetujui" {{ $detail['status_approval'] === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                                                        <option value="ditolak" {{ $detail['status_approval'] === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Catatan (Opsional)</label>
                                                    <textarea name="catatan_kaprodi" class="form-control" rows="3" placeholder="Tambahkan catatan jika ditolak atau perlu revisi">{{ $detail['catatan_kaprodi'] }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary">Simpan Keputusan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Belum ada rincian barang yang ditambahkan pada draf ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                        @if(count($draftPengadaan['details'] ?? []) > 0)
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="2" class="text-end py-3">Total Estimasi Keseluruhan:</th>
                                <th class="text-end py-3 text-primary fw-bolder">Rp {{ number_format($totalEstimasi, 0, ',', '.') }}</th>
                                <th colspan="2"></th>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
            
            @if($draftPengadaan['status'] === 'diajukan')
            <div class="card-footer bg-white text-end border-top py-3">
                <form action="{{ route('draft-pengadaan.review.finalize', $draftPengadaan['id']) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin memfinalisasi draf ini? Draf yang sudah difinalisasi tidak dapat diubah lagi.')">
                    @csrf
                    @method('PUT')
                    
                    @php
                        // Check if all items are either approved or rejected
                        $allReviewed = true;
                        foreach($draftPengadaan['details'] ?? [] as $d) {
                            if($d['status_approval'] === 'pending') {
                                $allReviewed = false;
                                break;
                            }
                        }
                    @endphp
                    
                    @if(count($draftPengadaan['details'] ?? []) === 0)
                        <button type="button" class="btn btn-success" disabled>Finalisasi Draf</button>
                        <small class="d-block text-danger mt-1">Draf kosong.</small>
                    @elseif(!$allReviewed)
                        <button type="button" class="btn btn-success" disabled>Finalisasi Draf</button>
                        <small class="d-block text-danger mt-1">Masih ada barang dengan status Pending.</small>
                    @else
                        <button type="submit" class="btn btn-success"><i class="ti ti-check me-1"></i> Finalisasi Draf</button>
                    @endif
                </form>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
