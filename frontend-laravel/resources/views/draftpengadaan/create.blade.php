@extends('layouts.master')

@section('title', 'Buat Draf Pengadaan Barang - InApp Inventory Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="fs-3 mb-0">Buat Draf Pengadaan Barang</h1>
            <a href="{{ route('draft-pengadaan.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>
</div>

<form action="{{ route('draft-pengadaan.store') }}" method="POST">
    @csrf
    <div class="row g-4">
        <!-- Main Form Column -->
        <div class="col-lg-8">
            <!-- Form Header -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-dark mb-3">Informasi Draf</h5>
                    
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
                            value="{{ old('tahun') }}"
                            required
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
                        >{{ old('catatan') }}</textarea>
                    </div>

                    <div class="mb-3 mb-0">
                        <label class="form-label fw-semibold d-block">
                            Status saat ini: 
                            <span class="badge bg-info text-dark ms-1">Draft</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Add Item Form -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-dark mb-3">Tambah Barang ke Draf</h5>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="barang_id">
                            Pilih Barang (Opsional)
                        </label>
                        <select 
                            class="form-select" 
                            id="barang_id" 
                            name="barang_id"
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
                        <small class="text-muted block mt-1">Anda bisa langsung menambahkan satu barang pertama Anda sekarang, atau membiarkannya kosong dan menambahkannya nanti.</small>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="jumlah">
                                Jumlah Barang
                            </label>
                            <input 
                                class="form-control" 
                                id="jumlah" 
                                type="number" 
                                name="jumlah" 
                                placeholder="1"
                                min="1"
                            >
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="harga_estimasi">
                                Harga Estimasi (Rp)
                            </label>
                            <input 
                                class="form-control" 
                                id="harga_estimasi" 
                                type="number" 
                                name="harga_estimasi" 
                                placeholder="0"
                                min="0"
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

                    <button type="submit" class="btn btn-success w-100">
                        <i class="ti ti-plus me-1"></i> Buat Draf & Tambah Barang
                    </button>
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
                        <span class="fw-bold text-dark">-</span>
                    </div>

                    <div class="mb-3 pb-3 border-bottom d-flex justify-content-between align-items-center">
                        <span class="text-muted">Total Jenis Barang</span>
                        <span class="fw-bold text-dark">0 item</span>
                    </div>

                    <div class="mb-3 pb-3 border-bottom">
                        <span class="text-muted d-block mb-1">Total Estimasi Anggaran</span>
                        <span class="fs-4 fw-bold text-success">
                            Rp 0
                        </span>
                    </div>

                    <div class="mb-4 d-flex justify-content-between align-items-center">
                        <span class="text-muted">Status Draf</span>
                        <span class="badge bg-info text-dark">
                            Draft
                        </span>
                    </div>

                    <a 
                        href="{{ route('draft-pengadaan.index') }}"
                        class="btn btn-light w-100 py-2"
                    >
                        Batal & Kembali ke Daftar
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function toggleInventarisOptions() {
    const checkbox = document.getElementById('toggleInventaris');
    const options = document.getElementById('inventarisOptions');
    options.style.display = checkbox.checked ? 'block' : 'none';
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
</script>
@endsection
