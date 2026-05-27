@extends('layouts.master')

@section('title', 'Draf Pengadaan Barang - InApp Inventory Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fs-3 mb-0">Draf Pengadaan Barang</h1>
            <a href="{{ route('draft-pengadaan.create') }}" class="btn btn-primary">
                <i class="ti ti-plus me-1"></i> Buat Draf Baru
            </a>
        </div>
    </div>
</div>

<!-- Status Filter Tabs -->
<div class="row mb-4">
    <div class="col-12">
        <div class="btn-group" role="group" aria-label="Status Filter">
            <a href="?status=" class="btn {{ request('status') === null || request('status') === '' ? 'btn-primary' : 'btn-outline-primary' }}">
                Semua
            </a>
            <a href="?status=draft" class="btn {{ request('status') === 'draft' ? 'btn-primary' : 'btn-outline-primary' }}">
                Draft
            </a>
            <a href="?status=submitted" class="btn {{ request('status') === 'submitted' ? 'btn-primary' : 'btn-outline-primary' }}">
                Diajukan
            </a>
            <a href="?status=approved" class="btn {{ request('status') === 'approved' ? 'btn-primary' : 'btn-outline-primary' }}">
                Disetujui
            </a>
        </div>
    </div>
</div>

<!-- Draft List -->
@if(count($draftPengadaans) > 0)
    <div class="row g-4">
        @foreach($draftPengadaans as $draft)
            @php
                $statusFilter = request('status');
                if ($statusFilter && $draft['status'] !== $statusFilter) {
                    continue;
                }
            @endphp
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="card-title fw-bold mb-0">Draf Tahun {{ $draft['tahun'] }}</h5>
                            @php
                                $statusClass = match($draft['status'] ?? 'draft') {
                                    'approved', 'finalized' => 'bg-success text-white',
                                    'submitted', 'reviewed' => 'bg-info text-dark',
                                    default => 'bg-secondary text-white'
                                };
                            @endphp
                            <span class="badge {{ $statusClass }}">
                                {{ ucfirst($draft['status']) }}
                            </span>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted d-block">Total Barang</small>
                            <span class="fs-4 fw-bold text-primary">
                                {{ count($draft['details'] ?? []) }} item
                            </span>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted d-block">Total Estimasi</small>
                            <span class="fs-5 fw-semibold text-success">
                                Rp {{ number_format(array_sum(array_map(function($detail) { 
                                    return ($detail['harga_estimasi'] ?? 0) * ($detail['jumlah'] ?? 0); 
                                }, $draft['details'] ?? [])), 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="mb-3 pb-3 border-bottom">
                            <small class="text-muted d-block">Dibuat Oleh</small>
                            <span class="fw-medium small text-dark">{{ $draft['pengguna']['nama'] ?? '-' }}</span>
                        </div>

                        @if($draft['catatan'])
                            <div class="mb-3 bg-light p-2 rounded">
                                <small class="text-muted d-block">Catatan</small>
                                <p class="mb-0 small fst-italic text-secondary">{{ $draft['catatan'] }}</p>
                            </div>
                        @endif

                        <div class="mt-auto d-flex gap-2">
                            <a 
                                href="{{ route('draft-pengadaan.edit', $draft['id']) }}"
                                class="btn btn-outline-primary btn-sm flex-grow-1"
                            >
                                Edit
                            </a>
                            <a 
                                href="{{ route('draft-pengadaan.show', $draft['id']) }}"
                                class="btn btn-outline-success btn-sm flex-grow-1"
                            >
                                Lihat
                            </a>
                            <form action="{{ route('draft-pengadaan.destroy', $draft['id']) }}" method="POST" class="flex-grow-1 mb-0">
                                @csrf
                                @method('DELETE')
                                <button 
                                    type="submit"
                                    onclick="return confirm('Yakin ingin menghapus draf ini?')"
                                    class="btn btn-outline-danger btn-sm w-100"
                                >
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="row">
        <div class="col-12">
            <div class="card text-center p-5 border-dashed">
                <div class="card-body">
                    <i class="ti ti-file-text fs-1 text-muted mb-3 d-block"></i>
                    <h4 class="fw-bold text-dark mb-2">Belum Ada Draf Pengadaan</h4>
                    <p class="text-muted mb-4">Mulai buat draf pengadaan barang untuk tahun ini</p>
                    <a href="{{ route('draft-pengadaan.create') }}" class="btn btn-primary">
                        Buat Draf Baru
                    </a>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
