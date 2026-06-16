@extends('layouts.master')

@section('title', 'Penerimaan Barang - InApp Inventory Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="fs-3 mb-0">Penerimaan Barang (Draf #{{ $draftPengadaan['id'] }})</h1>
            <a href="{{ route('draft-pengadaan.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>
</div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Draft Detail Header -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="card-title fw-semibold mb-3">Informasi Draf</h5>
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td class="text-muted" width="150">ID Draf</td>
                            <td class="fw-semibold">#{{ $draftPengadaan['id'] ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tahun</td>
                            <td class="fw-semibold">{{ $draftPengadaan['tahun'] ?? '-' }}</td>
                        </tr>

                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Barang List -->
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0 text-white">Daftar Barang untuk Diterima</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table text-nowrap mb-0 align-middle table-hover">
                    <thead class="text-dark fs-4">
                        <tr class="bg-light">
                            <th class="border-bottom-0"><h6 class="fw-semibold mb-0">No</h6></th>
                            <th class="border-bottom-0"><h6 class="fw-semibold mb-0">Barang</h6></th>
                            <th class="border-bottom-0"><h6 class="fw-semibold mb-0">Kategori</h6></th>
                            <th class="border-bottom-0 text-center"><h6 class="fw-semibold mb-0">Dipesan</h6></th>
                            <th class="border-bottom-0 text-center"><h6 class="fw-semibold mb-0">Diterima</h6></th>
                            <th class="border-bottom-0 text-center"><h6 class="fw-semibold mb-0">Status</h6></th>
                            <th class="border-bottom-0 text-center"><h6 class="fw-semibold mb-0">Aksi</h6></th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @forelse($draftPengadaan['details'] ?? [] as $detail)
                            @if(($detail['status_approval'] ?? '') !== 'disetujui')
                                @continue
                            @endif
                            @php
                                $dipesan = (int)($detail['jumlah'] ?? 0);
                                $diterima = (int)($detail['jumlah_diterima'] ?? 0);
                                $sisa = $dipesan - $diterima;
                                $isLengkap = $diterima >= $dipesan;
                            @endphp
                            <tr>
                                <td class="px-4 py-3">{{ $no++ }}</td>
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="ms-3">
                                            <h6 class="fw-semibold mb-1">{{ $detail['barang']['nama_barang'] ?? '-' }}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">{{ $detail['barang']['kategori']['nama_kategori'] ?? '-' }}</td>
                                <td class="px-4 py-3 text-center fw-bold">{{ $dipesan }}</td>
                                <td class="px-4 py-3 text-center text-success fw-bold">{{ $diterima }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if($isLengkap)
                                        <span class="badge bg-success rounded-3 fw-semibold">Lengkap</span>
                                    @else
                                        <span class="badge bg-warning rounded-3 fw-semibold">Sisa {{ $sisa }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if(!$isLengkap)
                                        <button type="button" class="btn btn-sm btn-primary" 
                                            data-bs-toggle="modal" data-bs-target="#terimaModal"
                                            onclick="setModalData({{ $detail['id'] }}, '{{ addslashes($detail['barang']['nama_barang'] ?? '') }}', {{ $sisa }})">
                                            <i class="ti ti-download"></i> Terima
                                        </button>
                                    @else
                                        <button class="btn btn-sm btn-secondary" disabled>Selesai</button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        @if($no === 1)
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Tidak ada barang yang disetujui untuk diterima.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<!-- Modal Terima Barang -->
<div class="modal fade" id="terimaModal" tabindex="-1" aria-labelledby="terimaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('draft-pengadaan.terima.store', $draftPengadaan['id'] ?? 0) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white" id="terimaModalLabel">Proses Penerimaan Barang</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="draft_pengadaan_detail_id" id="modal_detail_id">
                    
                    <div class="mb-4">
                        <h6 class="fw-semibold">Barang: <span id="modal_nama_barang" class="text-primary"></span></h6>
                        <p class="mb-2 text-muted">Sisa barang yang belum diterima: <span id="modal_sisa_barang" class="fw-bold"></span></p>
                    </div>

                    <div class="mb-3">
                        <label for="jumlah_terima" class="form-label">Jumlah Diterima Saat Ini</label>
                      <input type="number" class="form-control" id="jumlah_terima" min="1" value="1" onchange="generateForms()">
                        <small class="form-text text-muted">Masukkan berapa barang yang datang saat ini untuk melengkapi form inventarisnya.</small>
                    </div>

                    <div class="mb-3 form-check bg-light p-3 rounded border">
                        <input type="checkbox" class="form-check-input ms-1" id="is_bhp" name="is_bhp" value="1">
                        <label class="form-check-label ms-2 fw-semibold text-dark" for="is_bhp">Barang ini termasuk BHP (Bahan Habis Pakai)</label>
                        <small class="d-block ms-2 text-muted mt-1">Jika dicentang, stok barang ini akan otomatis ditambahkan ke tabel Stok BHP.</small>
                    </div>

                    <hr>

                    <div id="dynamic_forms_container">
                        <!-- Formulir inventaris akan di-generate di sini oleh JavaScript -->
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan ke Inventaris</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Template form item -->
<template id="item_form_template">
    <div class="card border mb-3 form-item-card">
        <div class="card-header bg-light py-2">
            <h6 class="mb-0 fw-semibold">Data Inventaris Item #<span class="item_index_label"></span></h6>
        </div>
        <div class="card-body py-3">
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label text-primary fw-semibold"><i class="ti ti-qrcode me-1"></i>QR Code Sistem (Otomatis)</label>
                    <div class="d-flex align-items-center gap-3 bg-primary-subtle p-2 rounded border border-primary-subtle">
                        <div class="bg-white p-1 rounded shadow-sm" style="width: 70px; height: 70px;">
                            <img src="" class="item_qr_img w-100 h-100 object-fit-contain" alt="QR Code">
                        </div>
                        <div class="flex-grow-1">
                            <span class="text-muted text-xs d-block mb-1">Dapat ditempel pada barang:</span>
                            <span class="item_qr_uuid fw-bold text-dark font-monospace text-sm"></span>
                            <input type="hidden" class="item_qr_hidden" value="">
                        </div>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="printQR(this)" title="Cetak QR Code ini">
                                <i class="ti ti-printer"></i> Cetak
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Foto QR Code / Barcode (Kampus)</label>
                    <input type="file" class="form-control item_qr_file" accept="image/*">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tanggal Masuk</label>
                    <input type="date" class="form-control item_tanggal" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Penempatan Ruangan</label>
                    <select class="form-select item_ruangan">
                        <option value="">-- Pilih Ruangan (Opsional) --</option>
                        @foreach($ruangan as $r)
                            <option value="{{ $r['id'] }}">{{ $r['nama_ruangan'] }} ({{ $r['kode_ruangan'] }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</template>

@endsection

@section('scripts')
<script>
    let currentSisa = 0;

    function setModalData(detailId, namaBarang, sisa) {
        document.getElementById('modal_detail_id').value = detailId;
        document.getElementById('modal_nama_barang').innerText = namaBarang;
        document.getElementById('modal_sisa_barang').innerText = sisa;
        document.getElementById('jumlah_terima').max = sisa;
        document.getElementById('jumlah_terima').value = 1;
        currentSisa = sisa;
        
        generateForms();
    }

    function generateForms() {
        const container = document.getElementById('dynamic_forms_container');
        const template = document.getElementById('item_form_template');
        let count = parseInt(document.getElementById('jumlah_terima').value) || 0;
        
        if (count > currentSisa) {
            count = currentSisa;
            document.getElementById('jumlah_terima').value = count;
        }

        if (count < 1) {
            count = 1;
            document.getElementById('jumlah_terima').value = 1;
        }

        container.innerHTML = ''; // Clear existing forms
        
        for (let i = 0; i < count; i++) {
            const clone = template.content.cloneNode(true);
            
            // Set index text
            clone.querySelector('.item_index_label').innerText = (i + 1);
            
            // Generate UUID and render QR Code
            let uuid = (typeof crypto !== 'undefined' && crypto.randomUUID) ? crypto.randomUUID() : 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
                return v.toString(16);
            });
            
            clone.querySelector('.item_qr_img').src = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' + encodeURIComponent(uuid);
            clone.querySelector('.item_qr_uuid').innerText = uuid;
            clone.querySelector('.item_qr_hidden').value = uuid;
            clone.querySelector('.item_qr_hidden').name = `items[${i}][qr_code]`;
            
            // Set name attributes dynamically
            clone.querySelector('.item_qr_file').name = `items[${i}][qr_code_kampus]`;
            clone.querySelector('.item_tanggal').name = `items[${i}][tanggal_masuk]`;
            clone.querySelector('.item_ruangan').name = `items[${i}][ruangan_id]`;
            
            container.appendChild(clone);
        }
    }

    function printQR(btn) {
        const container = btn.closest('.d-flex');
        const imgSrc = container.querySelector('.item_qr_img').src;
        const uuid = container.querySelector('.item_qr_uuid').innerText;
        const itemName = document.getElementById('modal_nama_barang').innerText;
        
        const printWindow = window.open('', '_blank', 'width=400,height=500');
        printWindow.document.write(`
            <html>
                <head>
                    <title>Cetak QR Code</title>
                    <style>
                        body { font-family: sans-serif; text-align: center; padding: 30px; margin: 0; }
                        .qr-container { border: 2px dashed #000; padding: 20px; display: inline-block; border-radius: 8px; }
                        img { width: 150px; height: 150px; }
                        h4 { margin: 10px 0 5px 0; font-size: 16px; font-weight: bold; }
                        p { margin: 0; font-size: 11px; color: #333; word-break: break-all; width: 150px; }
                        .system-label { font-size: 10px; color: #666; margin-top: 10px; border-top: 1px solid #eee; padding-top: 5px; }
                    </style>
                </head>
                <body>
                    <div class="qr-container">
                        <h4>${itemName}</h4>
                        <img src="${imgSrc}" onload="setTimeout(() => { window.print(); window.close(); }, 500);" />
                        <p>${uuid}</p>
                        <div class="system-label">InApp Inventory System</div>
                    </div>
                </body>
            </html>
        `);
        printWindow.document.close();
    }
</script>
@endsection
