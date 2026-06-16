@extends('layouts.master')

@section('title', 'Daftar Inventaris - InApp Inventory Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="fs-3 mb-1">Daftar Inventaris</h1>
                <p class="text-muted mb-0">Kelola dan pantau barang inventaris di seluruh laboratorium.</p>
            </div>
            @if(Session::has('user') && in_array(Session::get('user')['role'], ['staf administrasi', 'staf laboratorium']))
                <a href="{{ route('inventaris.create') }}" class="btn btn-primary">
                    <i class="ti ti-plus me-1"></i> Tambah Inventaris
                </a>
            @endif
        </div>
    </div>
</div>

<!-- Filter Card (All & Per Ruangan) -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body p-3">
                <form method="GET" action="{{ request()->url() }}" class="row g-3 align-items-end">
                    <div class="col-md-8">
                        <label for="ruangan_id" class="form-label text-xs fw-bold text-uppercase text-secondary mb-1">Saring Berdasarkan Ruangan</label>
                        <select name="ruangan_id" id="ruangan_id" class="form-select">
                            <option value="">-- Semua Ruangan (All) --</option>
                            @foreach($ruangan ?? [] as $r)
                                <option value="{{ $r['id'] }}" {{ request('ruangan_id') == $r['id'] ? 'selected' : '' }}>
                                    {{ $r['nama_ruangan'] }} ({{ $r['kode_ruangan'] }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="ti ti-filter me-1"></i> Saring
                        </button>
                        @if(request('ruangan_id'))
                            <a href="{{ request()->url() }}" class="btn btn-light w-100">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Pending Verification Section (Special Alert for Admin) -->
@if(Session::has('user') && Session::get('user')['role'] === 'staf administrasi')
    @php
        $pendingItems = array_filter($inventaris, function($item) {
            return ($item['status_verifikasi'] ?? '') === 'pending';
        });
    @endphp

    @if(count($pendingItems) > 0)
        <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center mb-4 py-3" role="alert">
            <i class="ti ti-alert-triangle fs-2 me-3 text-warning"></i>
            <div>
                <h5 class="alert-heading fw-bold mb-1 text-warning-emphasis">Persetujuan Kondisi Barang Tertunda</h5>
                <span>Ada <strong>{{ count($pendingItems) }}</strong> laporan kondisi barang baru dari Staf Lab yang perlu diverifikasi.</span>
            </div>
        </div>
    @endif
@endif

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr class="text-xs text-uppercase font-semibold">
                                <th class="px-3 py-2">Barang</th>
                                <th class="px-3 py-2">Ruangan</th>
                                <th class="px-3 py-2 text-center">QR Code</th>
                                <th class="px-3 py-2">Kondisi & Foto</th>
                                <th class="px-3 py-2">Status & Verifikasi</th>
                                @if(Session::has('user') && in_array(Session::get('user')['role'], ['staf administrasi', 'staf laboratorium']))
                                    <th class="px-3 py-2 text-end">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @foreach($inventaris as $item)
                            @php
                                $statusClass = match ($item['status_inventaris'] ?? 'tersedia') {
                                    'tersedia' => 'bg-success',
                                    'dipinjam' => 'bg-warning text-dark',
                                    'rusak' => 'bg-danger',
                                    default => 'bg-secondary'
                                };
                                
                                $isPending = ($item['status_verifikasi'] ?? '') === 'pending';
                                $isDitolak = ($item['status_verifikasi'] ?? '') === 'ditolak';
                            @endphp
                            <tr class="{{ $isPending ? 'table-warning' : '' }}">
                                <!-- Barang -->
                                <td class="px-3 py-2">
                                    <div class="fw-bold text-dark">{{ $item['barang']['nama_barang'] ?? 'Tidak Diketahui' }}</div>
                                </td>

                                <!-- Ruangan -->
                                <td class="px-3 py-2">
                                    <div class="fw-semibold text-dark">{{ $item['ruangan']['kode_ruangan'] ?? '-' }}</div>
                                    <small class="text-muted text-xs d-block">{{ $item['ruangan']['nama_ruangan'] ?? '' }}</small>
                                </td>

                                <!-- QR Code -->
                                <td class="px-3 py-2 text-center">
                                    <div class="d-flex flex-column gap-1 align-items-center">
                                        @if(!empty($item['qr_code_kampus']))
                                            <button class="btn btn-sm btn-outline-secondary py-0 px-2 text-xs" data-bs-toggle="modal" data-bs-target="#imageModal" onclick="document.getElementById('modalImg').src = '/{{ ltrim($item['qr_code_kampus'], '/') }}'; document.getElementById('imageModalLabel').innerText = 'QR Kampus: {{ addslashes($item['barang']['nama_barang'] ?? '') }}';">
                                                <i class="ti ti-qrcode me-1"></i> Kampus
                                            </button>
                                        @endif
                                        @if(!empty($item['qr_code']))
                                            <button class="btn btn-sm btn-outline-primary py-0 px-2 text-xs" data-bs-toggle="modal" data-bs-target="#imageModal" onclick="document.getElementById('modalImg').src = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' + encodeURIComponent('{{ $item['qr_code'] }}'); document.getElementById('imageModalLabel').innerText = 'QR Sistem: {{ addslashes($item['barang']['nama_barang'] ?? '') }}';">
                                                <i class="ti ti-qrcode me-1"></i> Sistem
                                            </button>
                                        @endif
                                    </div>
                                </td>

                                <!-- Kondisi & Foto -->
                                <td class="px-3 py-2">
                                    <div class="text-xs">
                                        <span class="fw-semibold text-secondary">Saat ini:</span>
                                        <span class="fw-bold text-dark">{{ ucfirst($item['kondisi'] ?? 'Baik') }}</span>
                                        @if(!empty($item['foto_barang']))
                                            <button class="btn btn-sm btn-link p-0 text-decoration-none ms-1 text-xs" data-bs-toggle="modal" data-bs-target="#imageModal" onclick="document.getElementById('modalImg').src = '/{{ ltrim($item['foto_barang'], '/') }}'; document.getElementById('imageModalLabel').innerText = '{{ addslashes($item['barang']['nama_barang'] ?? 'Foto') }}';">
                                                <i class="ti ti-photo me-1"></i>Foto
                                            </button>
                                        @endif
                                    </div>
                                </td>

                                <!-- Status & Verifikasi -->
                                <td class="px-3 py-2">
                                    <div class="d-flex flex-column gap-1 align-items-start">
                                        <span class="badge {{ $statusClass }} text-xs">
                                            {{ ucfirst($item['status_inventaris'] ?? 'tersedia') }}
                                        </span>
                                        
                                        @if($isPending)
                                            <span class="badge bg-warning text-dark text-xs"><i class="ti ti-hourglass-low me-1"></i>Pending</span>
                                        @elseif($isDitolak)
                                            <span class="badge bg-danger text-xs"><i class="ti ti-x me-1"></i>Ditolak</span>
                                        @else
                                            <span class="badge bg-success-subtle text-success border border-success-subtle text-xs"><i class="ti ti-circle-check me-1"></i>Terverifikasi</span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Aksi -->
                                @if(Session::has('user') && in_array(Session::get('user')['role'], ['staf administrasi', 'staf laboratorium']))
                                <td class="px-3 py-2 text-end">
                                    <div class="d-flex justify-content-end gap-1 flex-wrap">
                                        <!-- Staf Lab Upload Condition Action -->
                                        @if(Session::has('user') && Session::get('user')['role'] === 'staf laboratorium' && !$isPending)
                                            <a href="{{ route('inventaris.upload-kondisi', $item['id']) }}" class="btn btn-xs btn-outline-warning py-1 px-2 text-xs" title="Unggah Kondisi">
                                                <i class="ti ti-upload me-1"></i>Lapor
                                            </a>
                                        @endif

                                        <!-- Staf Admin Verification Actions -->
                                        @if(Session::has('user') && Session::get('user')['role'] === 'staf administrasi' && $isPending)
                                            <button type="button" class="btn btn-xs btn-primary py-1 px-2 text-xs" 
                                                data-bs-toggle="modal"
                                                data-bs-target="#verifikasiModal"
                                                data-name="{{ $item['barang']['nama_barang'] ?? '' }}"
                                                data-photo="{{ !empty($item['foto_pending']) ? '/' . ltrim($item['foto_pending'], '/') : '' }}"
                                                data-desc="{{ $item['kondisi_pending'] ?? 'Tidak ada keterangan tambahan.' }}"
                                                data-url="{{ route('inventaris.verifikasi', $item['id']) }}">
                                                <i class="ti ti-file-search me-1"></i>Tinjau Laporan
                                            </button>
                                        @endif

                                        <!-- Staf Lab and Staf Admin CRUD Actions -->
                                        @if(Session::has('user') && in_array(Session::get('user')['role'], ['staf administrasi', 'staf laboratorium']))
                                            <a href="{{ route('inventaris.edit', $item['id']) }}" class="btn btn-xs btn-outline-info py-1 px-2 text-xs" title="Edit">
                                                Edit
                                            </a>
                                            <form action="{{ route('inventaris.destroy', $item['id']) }}" method="POST" class="d-inline mb-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-xs btn-outline-danger py-1 px-2 text-xs" onclick="return confirm('Hapus barang ini?')" title="Hapus">
                                                    Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                                @endif
                            </tr>
                            @endforeach

                            @if(count($inventaris) === 0)
                            <tr>
                                <td colspan="{{ Session::has('user') && in_array(Session::get('user')['role'], ['staf administrasi', 'staf laboratorium']) ? 6 : 5 }}" class="text-center py-5 text-muted">
                                    <i class="ti ti-archive fs-1 mb-2 d-block text-gray-300"></i>
                                    Belum ada data barang inventaris.
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

<!-- Modal Preview Foto -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow-lg border-0">
      <div class="modal-header bg-light">
        <h5 class="modal-title fw-bold" id="imageModalLabel">Foto Barang</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center p-4">
        <img src="" id="modalImg" class="img-fluid rounded border shadow-sm" alt="Preview" style="max-height: 400px; object-fit: contain;">
        <div id="modalDescContainer" class="mt-4 text-start bg-light p-3 rounded border d-none">
            <h6 class="fw-bold mb-1 text-primary"><i class="ti ti-info-circle me-1"></i>Keterangan Kondisi:</h6>
            <p id="modalDesc" class="mb-0 text-dark"></p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Verifikasi -->
<div class="modal fade" id="verifikasiModal" tabindex="-1" aria-labelledby="verifikasiModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content shadow-lg border-0">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-bold text-white" id="verifikasiModalLabel">Tinjau Laporan Kondisi</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <div class="row">
            <div class="col-md-6 text-center mb-4 mb-md-0 d-flex flex-column justify-content-center bg-light rounded border p-2">
                <span class="text-muted small mb-2 d-block" id="verifikasiNoPhotoText">Memuat foto...</span>
                <img src="" id="verifikasiImg" class="img-fluid rounded shadow-sm mx-auto" alt="Bukti Foto" style="max-height: 300px; object-fit: contain; display: none;">
            </div>
            <div class="col-md-6">
                <h6 class="fw-bold mb-2 text-primary"><i class="ti ti-info-circle me-1"></i>Keterangan Dilaporkan:</h6>
                <div class="bg-primary-subtle p-3 rounded border border-primary-subtle mb-4">
                    <p id="verifikasiDesc" class="mb-0 text-dark fw-medium"></p>
                </div>
                
                <hr>
                
                <h6 class="fw-bold mb-2 text-dark">Aksi Keputusan:</h6>
                <p class="text-muted small mb-3">Silakan tinjau bukti di atas. Apakah Anda menyetujui perubahan kondisi barang ini?</p>
                <div class="d-flex gap-2">
                    <form id="formSetuju" method="POST" class="w-50 m-0">
                        @csrf
                        <input type="hidden" name="action" value="approve">
                        <button type="submit" class="btn btn-success w-100 shadow-sm" onclick="return confirm('Yakin ingin menyetujui laporan ini?')">
                            <i class="ti ti-check me-1"></i>Setujui Laporan
                        </button>
                    </form>
                    <form id="formTolak" method="POST" class="w-50 m-0">
                        @csrf
                        <input type="hidden" name="action" value="reject">
                        <button type="submit" class="btn btn-outline-danger w-100 bg-white shadow-sm" onclick="return confirm('Yakin ingin menolak laporan ini?')">
                            <i class="ti ti-x me-1"></i>Tolak Laporan
                        </button>
                    </form>
                </div>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
function showImage(src, title) {
    document.getElementById('modalImg').style.display = 'inline-block';
    document.getElementById('modalImg').src = '/' + src;
    document.getElementById('imageModalLabel').innerText = title;
    document.getElementById('modalDescContainer').classList.add('d-none');
    var myModal = new bootstrap.Modal(document.getElementById('imageModal'));
    myModal.show();
}

function showQR(data, title) {
    document.getElementById('modalImg').style.display = 'inline-block';
    document.getElementById('modalImg').src = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' + encodeURIComponent(data);
    document.getElementById('imageModalLabel').innerText = title;
    document.getElementById('modalDescContainer').classList.add('d-none');
    var myModal = new bootstrap.Modal(document.getElementById('imageModal'));
    myModal.show();
}

function showPendingDetails(src, title, desc) {
    document.getElementById('imageModalLabel').innerText = title;
    
    if (src) {
        document.getElementById('modalImg').src = src;
        document.getElementById('modalImg').style.display = 'inline-block';
    } else {
        document.getElementById('modalImg').style.display = 'none';
        document.getElementById('modalImg').src = '';
    }
    
    if (desc) {
        document.getElementById('modalDesc').innerText = desc;
        document.getElementById('modalDescContainer').classList.remove('d-none');
    } else {
        document.getElementById('modalDescContainer').classList.add('d-none');
    }
    
    var myModal = new bootstrap.Modal(document.getElementById('imageModal'));
    myModal.show();
}
document.addEventListener('DOMContentLoaded', function () {
    const verifikasiModalEl = document.getElementById('verifikasiModal');
    if (verifikasiModalEl) {
        verifikasiModalEl.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const itemName = button.getAttribute('data-name');
            const src = button.getAttribute('data-photo');
            const desc = button.getAttribute('data-desc');
            const actionUrl = button.getAttribute('data-url');

            document.getElementById('verifikasiModalLabel').innerText = 'Tinjau Laporan: ' + itemName;
            
            if (src && src !== '/') {
                document.getElementById('verifikasiImg').src = src;
                document.getElementById('verifikasiImg').style.display = 'block';
                document.getElementById('verifikasiNoPhotoText').style.display = 'none';
            } else {
                document.getElementById('verifikasiImg').style.display = 'none';
                document.getElementById('verifikasiImg').src = '';
                document.getElementById('verifikasiNoPhotoText').style.display = 'block';
                document.getElementById('verifikasiNoPhotoText').innerText = 'Tidak ada foto bukti yang dilampirkan.';
            }
            
            document.getElementById('verifikasiDesc').innerText = desc || 'Tidak ada keterangan tambahan.';
            
            document.getElementById('formSetuju').action = actionUrl;
            document.getElementById('formTolak').action = actionUrl;
        });
    }
});
</script>
@endsection
