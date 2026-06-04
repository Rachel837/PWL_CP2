@extends('layouts.master')

@section('title', 'Draf Pengadaan Barang - InApp Inventory Dashboard')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="fs-3 mb-0">Draf Pengadaan Barang</h1>
                @if(Session::has('user') && Session::get('user')['role'] === 'kepala laboratorium')
                    <a href="{{ route('draft-pengadaan.create') }}" class="btn btn-primary">
                        <i class="ti ti-plus me-1"></i> Buat Draf Baru
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Status Filter Tabs -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="btn-group" role="group" aria-label="Status Filter">
                <a href="?status="
                    class="btn {{ request('status') === null || request('status') === '' ? 'btn-primary' : 'btn-outline-primary' }}">
                    Semua
                </a>
                <a href="?status=draft"
                    class="btn {{ request('status') === 'draft' ? 'btn-primary' : 'btn-outline-primary' }}">
                    Draft
                </a>
                <a href="?status=diajukan"
                    class="btn {{ request('status') === 'diajukan' ? 'btn-primary' : 'btn-outline-primary' }}">
                    Diajukan
                </a>
                <a href="?status=disetujui"
                    class="btn {{ request('status') === 'disetujui' ? 'btn-primary' : 'btn-outline-primary' }}">
                    Disetujui
                </a>
                <a href="?status=ditolak"
                    class="btn {{ request('status') === 'ditolak' ? 'btn-danger' : 'btn-outline-danger' }}">
                    Ditolak
                </a>
            </div>
        </div>
    </div>

    <!-- Draft List -->
    @if(count($draftPengadaans) > 0)
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3">Tahun</th>
                                <th class="px-4 py-3">Total Barang</th>
                                <th class="px-4 py-3">Total Estimasi</th>
                                <th class="px-4 py-3">Dibuat Oleh</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $hasData = false;
                            @endphp
                            @foreach($draftPengadaans as $draft)
                                            @php
                                                $statusFilter = request('status');
                                                if ($statusFilter && $draft['status'] !== $statusFilter) {
                                                    continue;
                                                }
                                                $hasData = true;
                                                $statusClass = match ($draft['status'] ?? 'draft') {
                                                    'disetujui' => 'bg-success',
                                                    'diajukan' => 'bg-info text-dark',
                                                    'ditolak' => 'bg-danger',
                                                    default => 'bg-secondary'
                                                };
                                            @endphp
                                            <tr>
                                                <td class="px-4 py-3 fw-bold">{{ $draft['tahun'] }}</td>
                                                <td class="px-4 py-3">{{ count($draft['details'] ?? []) }} item</td>
                                                <td class="px-4 py-3 text-success fw-semibold">
                                                    Rp {{ number_format(array_sum(array_map(function ($detail) {
                                    return ($detail['harga_estimasi'] ?? 0) * ($detail['jumlah'] ?? 0);
                                }, $draft['details'] ?? [])), 0, ',', '.') }}
                                                </td>
                                                <td class="px-4 py-3">{{ $draft['pengguna']['nama'] ?? '-' }}</td>
                                                <td class="px-4 py-3">
                                                    <span class="badge {{ $statusClass }}">
                                                        {{ ucfirst($draft['status'] ?? 'draft') }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <div class="d-flex justify-content-center gap-2">
                                                        <a href="{{ route('draft-pengadaan.show', $draft['id']) }}"
                                                            class="btn btn-sm btn-outline-info" title="Detail">
                                                            <i class="ti ti-info-circle"></i> Detail
                                                        </a>
                                                        @if(Session::has('user') && Session::get('user')['role'] === 'kepala laboratorium')
                                                            <a href="{{ route('draft-pengadaan.edit', $draft['id']) }}"
                                                                class="btn btn-sm btn-outline-success" title="Lihat">
                                                                <i class="ti ti-eye"></i> Lihat
                                                            </a>
                                                            <form action="{{ route('draft-pengadaan.destroy', $draft['id']) }}" method="POST"
                                                                class="mb-0">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" onclick="return confirm('Yakin ingin menghapus draf ini?')"
                                                                    class="btn btn-sm btn-outline-danger" title="Hapus">
                                                                    <i class="ti ti-trash"></i> Hapus
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                            @endforeach

                            @if(!$hasData)
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Tidak ada draf pengadaan yang sesuai dengan
                                        filter.</td>
                                </tr>
                            @endif
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
                        <i class="ti ti-file-text fs-1 text-muted mb-3 d-block"></i>
                        <h4 class="fw-bold text-dark mb-2">Belum Ada Draf Pengadaan</h4>
                        <p class="text-muted mb-4">Belum ada draf pengadaan barang saat ini.</p>
                        @if(Session::has('user') && Session::get('user')['role'] === 'kepala laboratorium')
                            <a href="{{ route('draft-pengadaan.create') }}" class="btn btn-primary">
                                Buat Draf Baru
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection