@extends('layouts.master')

@section('title', 'Review Draf Pengadaan - InApp Inventory Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fs-3 mb-0">Review Draf Pengadaan</h1>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3 text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ID</th>
                                <th class="px-4 py-3 text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tahun</th>
                                <th class="px-4 py-3 text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kepala Lab</th>
                                <th class="px-4 py-3 text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                <th class="px-4 py-3 text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tanggal Diajukan</th>
                                <th class="px-4 py-3 text-end text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($draftPengadaans as $draft)
                            @php
                                $statusClass = match($draft['status'] ?? 'diajukan') {
                                    'disetujui' => 'bg-success',
                                    'diajukan' => 'bg-info text-dark',
                                    'ditolak' => 'bg-danger',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <tr>
                                <td class="px-4 py-3 text-sm">
                                    #{{ $draft['id'] }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $draft['tahun'] }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $draft['pengguna']['nama'] ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="badge {{ $statusClass }}">
                                        {{ ucfirst($draft['status'] ?? 'diajukan') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ \Carbon\Carbon::parse($draft['created_at'])->format('d M Y') }}
                                </td>
                                <td class="px-4 py-3 text-end">
                                    @if($draft['status'] === 'diajukan')
                                    <a href="{{ route('draft-pengadaan.review.show', $draft['id']) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="ti ti-eye me-1"></i> Lihat & Review
                                    </a>
                                    @else
                                    <a href="{{ route('draft-pengadaan.review.show', $draft['id']) }}" class="btn btn-sm btn-outline-info">
                                        <i class="ti ti-file-text me-1"></i> Detail
                                    </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                            
                            @if(count($draftPengadaans) === 0)
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Tidak ada draf pengadaan yang perlu di-review.</td>
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
