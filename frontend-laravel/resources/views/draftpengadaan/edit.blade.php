@extends('layouts.master')

@section('title', 'Edit Draf Pengadaan Barang - InApp Inventory Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="fs-3 mb-0">Edit Draf Pengadaan Barang</h1>
            <a href="{{ route('draft-pengadaan.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Main Form Column -->
    <div class="col-lg-8">
        <!-- Form Header -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold text-dark mb-3">Informasi Draf</h5>
                @if($draftPengadaan['status'] === 'locked')
                    <div class="alert alert-warning py-2 mb-3">
                        <i class="ti ti-lock me-1"></i> Draf ini telah <strong>dikunci</strong> dan tidak dapat diubah lagi.
                    </div>
                @endif
                <form action="{{ route('draft-pengadaan.update', $draftPengadaan['id']) }}" method="POST">
                    @method('PUT')
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="tahun">
                            Tahun Pengadaan <span class="text-danger">*</span>
                        </label>
                        <input 
                            class="form-control" 
                            id="tahun" 
                            type="text" 
                            name="tahun" 
                            placeholder="Contoh: 2026"
                            value="{{ old('tahun', $draftPengadaan['tahun'] ?? '') }}"
                            required
                            {{ $draftPengadaan['status'] === 'locked' ? 'disabled' : '' }}
                        >
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="catatan">
                            Catatan
                        </label>
                        <textarea 
                            class="form-control" 
                            id="catatan" 
                            name="catatan" 
                            rows="3"
                            placeholder="Catatan tambahan untuk draf pengadaan ini"
                            {{ $draftPengadaan['status'] === 'locked' ? 'disabled' : '' }}
                        >{{ old('catatan', $draftPengadaan['catatan'] ?? '') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold d-block">
                            Status saat ini: 
                            <span class="badge bg-info text-dark ms-1">
                                {{ ucfirst($draftPengadaan['status']) }}
                            </span>
                        </label>
                    </div>


                </form>
            </div>
        </div>

        <!-- Add Item Form -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold text-dark mb-3">Tambah Barang ke Draf</h5>
                
                <form action="{{ route('draft-pengadaan.add-detail', $draftPengadaan['id']) }}" method="POST" id="addDetailForm">
                    @csrf

                    <input type="hidden" name="draft_pengadaan_id" value="{{ $draftPengadaan['id'] }}">

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="barang_id">
                            Pilih Barang <span class="text-danger">*</span>
                        </label>
                        <select 
                            class="form-select" 
                            id="barang_id" 
                            name="barang_id"
                            required
                            onchange="loadReplacementInventaris(this.value)"
                        >
                            <option value="">-- Pilih Barang --</option>
                            @foreach($barang as $item)
                                <option value="{{ $item['id'] }}">
                                    {{ $item['nama_barang'] }} 
                                    @if(isset($item['kategori']))
                                        ({{ $item['kategori']['nama_kategori'] }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="jumlah">
                                Jumlah Barang <span class="text-danger">*</span>
                            </label>
                            <input 
                                class="form-control" 
                                id="jumlah" 
                                type="number" 
                                name="jumlah" 
                                placeholder="1"
                                min="1"
                                required
                            >
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="harga_estimasi">
                                Harga Estimasi (Rp) <span class="text-danger">*</span>
                            </label>
                            <input 
                                class="form-control" 
                                id="harga_estimasi" 
                                type="number" 
                                name="harga_estimasi" 
                                placeholder="0"
                                min="0"
                                required
                            >
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="link_pembelian">
                            Link Pembelian
                        </label>
                        <input 
                            class="form-control" 
                            id="link_pembelian" 
                            type="url" 
                            name="link_pembelian" 
                            placeholder="https://example.com/product"
                        >
                    </div>

                    <div class="mb-4">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="toggleInventaris" onchange="toggleInventarisOptions()">
                            <label class="form-check-label fw-semibold" for="toggleInventaris">
                                Ganti Barang Inventaris Lama
                            </label>
                        </div>
                        <div id="inventarisOptions" style="display: none;" class="mt-2 p-3 bg-light rounded">
                            <select 
                                class="form-select" 
                                id="inventaris_id_lama" 
                                name="inventaris_id_lama"
                            >
                                <option value="">-- Pilih Barang Inventaris --</option>
                            </select>
                            <small class="text-muted block mt-2">Pilih barang inventaris lama yang akan digantikan dengan pembelian ini.</small>
                        </div>
                    </div>

                    <button 
                        type="submit" 
                        class="btn btn-success w-100"
                        {{ $draftPengadaan['status'] === 'locked' ? 'disabled' : '' }}
                    >
                        <i class="ti ti-check me-1"></i> Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Summary Column -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 sticky-top" style="top: 80px; z-index: 10;">
            <div class="card-body p-4">
                <h5 class="fw-bold text-dark mb-4">Ringkasan Draf</h5>
                
                <div class="mb-3 pb-3 border-bottom d-flex justify-content-between align-items-center">
                    <span class="text-muted">Tahun Pengadaan</span>
                    <span class="fw-bold text-dark">{{ $draftPengadaan['tahun'] ?? '-' }}</span>
                </div>

                <div class="mb-3 pb-3 border-bottom d-flex justify-content-between align-items-center">
                    <span class="text-muted">Total Jenis Barang</span>
                    <span class="fw-bold text-dark">{{ count($draftPengadaan['details'] ?? []) }} item</span>
                </div>

                <div class="mb-3 pb-3 border-bottom">
                    <span class="text-muted d-block mb-1">Total Estimasi Anggaran</span>
                    <span class="fs-4 fw-bold text-success">
                        Rp {{ number_format(array_sum(array_map(function($detail) { 
                            return ($detail['harga_estimasi'] ?? 0) * ($detail['jumlah'] ?? 0); 
                        }, $draftPengadaan['details'] ?? [])), 0, ',', '.') }}
                    </span>
                </div>

                <div class="mb-4 d-flex justify-content-between align-items-center">
                    <span class="text-muted">Status Draf</span>
                    <span class="badge bg-info text-dark">
                        {{ ucfirst($draftPengadaan['status'] ?? 'draft') }}
                    </span>
                </div>

                @if($draftPengadaan['status'] === 'draft')
                    <form action="{{ route('draft-pengadaan.submit', $draftPengadaan['id']) }}" method="POST" class="mt-4 mb-0">
                        @csrf
                        <button 
                            type="submit" 
                            class="btn btn-primary w-100 py-2 mb-2"
                        >
                            <i class="ti ti-send me-1"></i> Ajukan untuk Approval
                        </button>
                    </form>
                @endif

                <a 
                    href="{{ route('draft-pengadaan.index') }}"
                    class="btn btn-light w-100 py-2"
                >
                    Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Items Table -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h5 class="fw-bold text-dark mb-3">Daftar Barang yang Akan Dibeli</h5>
                
                @if(isset($draftPengadaan['details']) && count($draftPengadaan['details']) > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-3 py-2">Nama Barang</th>
                                    <th class="px-3 py-2">Kategori</th>
                                    <th class="px-3 py-2 text-center">Jumlah</th>
                                    <th class="px-3 py-2 text-end">Harga Estimasi</th>
                                    <th class="px-3 py-2 text-end">Subtotal</th>
                                    <th class="px-3 py-2 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($draftPengadaan['details'] as $detail)
                                    <tr>
                                        <td class="px-3 py-3">
                                            @if($detail['link_pembelian'])
                                                <a href="{{ $detail['link_pembelian'] }}" target="_blank" class="text-primary text-decoration-none fw-semibold">
                                                    {{ $detail['barang']['nama_barang'] ?? '-' }} <i class="ti ti-external-link small"></i>
                                                </a>
                                            @else
                                                <span class="fw-semibold">{{ $detail['barang']['nama_barang'] ?? '-' }}</span>
                                            @endif
                                            @if($detail['inventaris_lama'])
                                                <div class="mt-1">
                                                    <span class="badge bg-warning text-dark text-xxs">
                                                        Ganti: {{ $detail['inventaris_lama']['kode_inventaris'] }} ({{ $detail['inventaris_lama']['kondisi'] }})
                                                    </span>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-3 py-3">
                                            <span class="badge bg-secondary">
                                                {{ $detail['barang']['kategori']['nama_kategori'] ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-3 text-center fw-medium">{{ $detail['jumlah'] }}</td>
                                        <td class="px-3 py-3 text-end text-muted">Rp {{ number_format($detail['harga_estimasi'] ?? 0, 0, ',', '.') }}</td>
                                        <td class="px-3 py-3 text-end fw-semibold text-dark">
                                            Rp {{ number_format(($detail['harga_estimasi'] ?? 0) * ($detail['jumlah'] ?? 0), 0, ',', '.') }}
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            @if($draftPengadaan['status'] !== 'locked')
                                                <button 
                                                    type="button"
                                                    onclick="openEditModal({{ json_encode($detail) }})"
                                                    class="btn btn-sm btn-outline-primary me-1"
                                                >
                                                    Edit
                                                </button>
                                                <form action="{{ route('draft-pengadaan.delete-detail', $detail['id']) }}" method="POST" class="d-inline mb-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button 
                                                        type="submit"
                                                        onclick="return confirm('Yakin ingin menghapus barang ini?')"
                                                        class="btn btn-sm btn-outline-danger"
                                                    >
                                                        Hapus
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-muted"><i class="ti ti-lock"></i> Terkunci</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5 bg-light rounded border-dashed">
                        <i class="ti ti-package fs-1 text-muted mb-2 d-block"></i>
                        <p class="text-muted mb-0">Belum ada barang yang ditambahkan ke draft pengadaan ini.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" style="display: none;" class="modal-backdrop-custom fixed-top w-100 h-100 flex items-center justify-center align-items-center" style="background: rgba(0,0,0,0.5); z-index: 1050;">
    <div class="bg-white rounded p-4 w-100 shadow-lg" style="max-width: 500px; margin: 10% auto;">
        <h4 class="fw-bold text-dark mb-4 border-bottom pb-2">Edit Barang</h4>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label class="form-label fw-semibold">Jumlah Barang</label>
                <input 
                    type="number" 
                    id="editJumlah" 
                    name="jumlah" 
                    min="1" 
                    required
                    class="form-control"
                >
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Harga Estimasi (Rp)</label>
                <input 
                    type="number" 
                    id="editHarga" 
                    name="harga_estimasi" 
                    min="0" 
                    required
                    class="form-control"
                >
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Link Pembelian</label>
                <input 
                    type="url" 
                    id="editLink" 
                    name="link_pembelian"
                    class="form-control"
                >
            </div>

            <div class="mb-4">
                <div class="form-check mb-2">
                    <input type="checkbox" class="form-check-input" id="editToggleInventaris" onchange="toggleEditInventarisOptions()">
                    <label class="form-check-label fw-semibold" for="editToggleInventaris">Ganti Barang Inventaris Lama</label>
                </div>
                <div id="editInventarisOptions" style="display: none;" class="mt-2 p-2 bg-light rounded">
                    <select 
                        class="form-select" 
                        id="edit_inventaris_id_lama" 
                        name="inventaris_id_lama"
                    >
                        <option value="">-- Pilih Barang Inventaris --</option>
                    </select>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button 
                    type="submit"
                    class="btn btn-primary flex-grow-1"
                >
                    Simpan
                </button>
                <button 
                    type="button"
                    onclick="closeEditModal()"
                    class="btn btn-light flex-grow-1"
                >
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleInventarisOptions() {
    const checkbox = document.getElementById('toggleInventaris');
    const options = document.getElementById('inventarisOptions');
    options.style.display = checkbox.checked ? 'block' : 'none';
}

function toggleEditInventarisOptions() {
    const checkbox = document.getElementById('editToggleInventaris');
    const options = document.getElementById('editInventarisOptions');
    options.style.display = checkbox.checked ? 'block' : 'none';
    if (!checkbox.checked) {
        document.getElementById('edit_inventaris_id_lama').value = '';
    }
}

function loadReplacementInventaris(barangId) {
    if (!barangId) {
        document.getElementById('inventaris_id_lama').innerHTML = '<option value="">-- Pilih Barang Inventaris --</option>';
        return;
    }

    fetch(`{{ url('draft-pengadaan') }}/${barangId}/inventaris`)
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('inventaris_id_lama');
            let html = '<option value="">-- Pilih Barang Inventaris --</option>';
            
            data.forEach(inv => {
                html += `<option value="${inv.id}">
                    ${inv.kode_inventaris} - ${inv.barang?.nama_barang || ''} (${inv.kondisi})
                </option>`;
            });
            
            select.innerHTML = html;
        })
        .catch(error => console.error('Error:', error));
}

function openEditModal(detail) {
    document.getElementById('editJumlah').value = detail.jumlah;
    document.getElementById('editHarga').value = detail.harga_estimasi;
    document.getElementById('editLink').value = detail.link_pembelian || '';
    
    const barangId = detail.barang_id;
    const currentInvId = detail.inventaris_id;
    
    // Load replacement options for this barang
    fetch(`{{ url('draft-pengadaan') }}/${barangId}/inventaris`)
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('edit_inventaris_id_lama');
            let html = '<option value="">-- Pilih Barang Inventaris --</option>';
            
            data.forEach(inv => {
                const selected = inv.id == currentInvId ? 'selected' : '';
                html += `<option value="${inv.id}" ${selected}>
                    ${inv.kode_inventaris} - ${inv.barang?.nama_barang || ''} (${inv.kondisi})
                </option>`;
            });
            
            select.innerHTML = html;
            
            const checkbox = document.getElementById('editToggleInventaris');
            const optionsDiv = document.getElementById('editInventarisOptions');
            if (currentInvId) {
                checkbox.checked = true;
                optionsDiv.style.display = 'block';
            } else {
                checkbox.checked = false;
                optionsDiv.style.display = 'none';
            }
        })
        .catch(error => console.error('Error:', error));
        
    document.getElementById('editForm').action = `{{ url('draft-pengadaan-detail') }}/${detail.id}`;
    
    // Show custom modal
    const modal = document.getElementById('editModal');
    modal.style.display = 'block';
    modal.style.position = 'fixed';
    modal.style.backgroundColor = 'rgba(0,0,0,0.5)';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}
</script>
@endsection
