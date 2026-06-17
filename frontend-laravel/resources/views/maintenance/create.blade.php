@extends('layouts.master')

@section('title', 'Catat Maintenance - InApp Inventory Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="fs-3 mb-1">Catat Log Maintenance</h1>
                <p class="text-muted mb-0 text-sm">Catat perawatan barang inventaris dan kurangi stok BHP yang digunakan selama perbaikan.</p>
            </div>
            <a href="{{ route('maintenance.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
                <form id="maintenanceForm" action="{{ route('maintenance.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Filter Ruangan -->
                    <div class="mb-3">
                        <label for="filter_ruangan" class="form-label font-weight-bold">Filter Ruangan</label>
                        <select id="filter_ruangan" class="form-select">
                            <option value="">-- Semua Ruangan --</option>
                            @if(isset($ruanganList) && is_array($ruanganList))
                                @foreach($ruanganList as $ruangan)
                                    <option value="{{ $ruangan['id'] }}">{{ $ruangan['nama_ruangan'] }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <!-- 1. Pilihan Inventaris -->
                    <div class="mb-3">
                        <label for="inventaris_id" class="form-label font-weight-bold">Pilih Barang Inventaris <span class="text-danger">*</span></label>
                        <select name="inventaris_id" id="inventaris_id" class="form-select" required>
                            <option value="" disabled selected>-- Pilih Barang Inventaris --</option>
                            @foreach($inventarisList as $item)
                                <option value="{{ $item['id'] }}" data-kondisi="{{ $item['kondisi'] }}" data-ruangan-id="{{ $item['ruangan']['id'] ?? '' }}" {{ old('inventaris_id') == $item['id'] ? 'selected' : '' }}>
                                    {{ $item['barang']['nama_barang'] ?? '' }} ({{ $item['kode_inventaris'] ?? '-' }}) - Ruangan: {{ $item['ruangan']['nama_ruangan'] ?? 'Tanpa Ruangan' }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text text-xs mt-1">Hanya barang yang terdaftar di ruangan yang dapat dipilih.</div>
                    </div>

                    <!-- Foto Kondisi Sebelum -->
                    <div class="mb-3">
                        <label for="foto_before" class="form-label fw-semibold">Foto Kondisi Sebelum <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="foto_before" name="foto_before" accept="image/*" required>
                        <div class="form-text">Unggah foto bukti sebelum perbaikan dilakukan (Format: JPG/PNG, Maks: 2MB).</div>
                    </div>

                    <div class="row">
                        <!-- 2. Kondisi Sebelum -->
                        <div class="col-md-6 mb-3">
                            <label for="kondisi_sebelum_display" class="form-label">Kondisi Sebelum</label>
                            <input type="text" id="kondisi_sebelum_display" class="form-control bg-light" readonly placeholder="Pilih barang terlebih dahulu">
                            <input type="hidden" name="kondisi_sebelum" id="kondisi_sebelum">
                        </div>

                        <!-- 3. Kondisi Sesudah -->
                        <div class="col-md-6 mb-3">
                            <label for="kondisi_sesudah" class="form-label">Kondisi Sesudah <span class="text-danger">*</span></label>
                            <select name="kondisi_sesudah" id="kondisi_sesudah" class="form-select" required>
                                <option value="" disabled selected>-- Pilih Kondisi Baru --</option>
                                <option value="baik" {{ old('kondisi_sesudah') == 'baik' ? 'selected' : '' }}>Baik</option>
                                <option value="rusak ringan" {{ old('kondisi_sesudah') == 'rusak ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                                <option value="rusak berat" {{ old('kondisi_sesudah') == 'rusak berat' ? 'selected' : '' }}>Rusak Berat</option>
                            </select>
                        </div>
                    </div>

                    <!-- Foto Kondisi Setelah -->
                    <div class="mb-3">
                        <label for="foto_after" class="form-label fw-semibold">Foto Kondisi Setelah <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="foto_after" name="foto_after" accept="image/*" required>
                        <div class="form-text">Unggah foto bukti sesudah perbaikan dilakukan (Format: JPG/PNG, Maks: 2MB).</div>
                    </div>

                    <!-- 4. Tindakan -->
                    <div class="mb-3">
                        <label for="tindakan" class="form-label">Tindakan <span class="text-danger">*</span></label>
                        <textarea name="tindakan" id="tindakan" rows="3" class="form-control" placeholder="Jelaskan tindakan perbaikan yang dilakukan..." required>{{ old('tindakan') }}</textarea>
                    </div>

                    <!-- 5. Catatan -->
                    <div class="mb-4">
                        <label for="catatan" class="form-label">Catatan Tambahan</label>
                        <textarea name="catatan" id="catatan" rows="2" class="form-control" placeholder="Masukkan catatan opsional...">{{ old('catatan') }}</textarea>
                    </div>

                    <!-- 6. Penggunaan BHP (Dynamic) -->
                    <div class="mb-4 pt-3 border-top">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="mb-0 text-dark fw-bold text-sm">Bahan Habis Pakai (BHP) yang Digunakan</h5>
                                <p class="text-muted text-xs mb-0">Klik tombol di samping jika ada BHP yang terpakai selama proses maintenance.</p>
                            </div>
                            <button type="button" id="addBhpRow" class="btn btn-sm btn-outline-primary">
                                <i class="ti ti-plus"></i> Tambah BHP
                            </button>
                        </div>

                        <div id="bhpRowsContainer">
                            <!-- Baris dynamic BHP dimasukkan di sini -->
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="ti ti-device-floppy me-1"></i> Simpan Catatan Maintenance
                        </button>
                        <a href="{{ route('maintenance.index') }}" class="btn btn-outline-secondary px-3 ms-2">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Template Baris BHP (Hidden) -->
<div id="bhpRowTemplate" class="d-none">
    <div class="row align-items-end mb-3 bhp-row border p-2 rounded bg-light">
        <div class="col-12 col-md-7 mb-2 mb-md-0">
            <label class="form-label text-xs">Pilih Item BHP <span class="text-danger">*</span></label>
            <select class="form-select select-bhp" required>
                <option value="" disabled selected>-- Pilih BHP --</option>
                @foreach($bhpList as $bhp)
                    <option value="{{ $bhp['id'] }}" data-stok="{{ $bhp['jumlah_stok'] }}" data-satuan="{{ $bhp['barang']['satuan'] ?? '' }}">
                        {{ $bhp['barang']['nama_barang'] ?? '' }} (Tersedia: {{ $bhp['jumlah_stok'] }} {{ $bhp['barang']['satuan'] ?? '' }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-8 col-md-3">
            <label class="form-label text-xs">Jumlah Digunakan <span class="text-danger">*</span></label>
            <div class="input-group">
                <input type="number" class="form-control input-qty" min="1" required placeholder="0">
                <span class="input-group-text text-xs bg-light display-satuan">-</span>
            </div>
        </div>
        <div class="col-4 col-md-2 text-end">
            <button type="button" class="btn btn-outline-danger remove-bhp-row w-100">
                <i class="ti ti-trash"></i> Hapus
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const inventarisSelect = document.getElementById('inventaris_id');
    const kondisiSebelumDisplay = document.getElementById('kondisi_sebelum_display');
    const kondisiSebelumInput = document.getElementById('kondisi_sebelum');
    const filterRuanganSelect = document.getElementById('filter_ruangan');
    
    // Simpan semua opsi asli inventaris
    const originalInventarisOptions = Array.from(inventarisSelect.options);

    // Update kondisi sebelum ketika barang inventaris dipilih
    inventarisSelect.addEventListener('change', function() {
        if (inventarisSelect.selectedIndex === -1 || inventarisSelect.value === "") return;
        const selectedOption = inventarisSelect.options[inventarisSelect.selectedIndex];
        const kondisi = selectedOption.getAttribute('data-kondisi');
        kondisiSebelumDisplay.value = kondisi ? kondisi.toUpperCase() : '';
        kondisiSebelumInput.value = kondisi || '';
    });

    // Filter inventaris berdasarkan ruangan
    if (filterRuanganSelect) {
        filterRuanganSelect.addEventListener('change', function() {
            const selectedRuanganId = this.value;

            // Kosongkan select
            inventarisSelect.innerHTML = '';

            // Tambahkan kembali opsi default (pertama)
            if (originalInventarisOptions.length > 0) {
                inventarisSelect.appendChild(originalInventarisOptions[0].cloneNode(true));
            }

            // Tambahkan opsi yang sesuai dengan ruangan
            originalInventarisOptions.slice(1).forEach(option => {
                const optionRuanganId = option.getAttribute('data-ruangan-id');
                if (selectedRuanganId === "" || optionRuanganId === selectedRuanganId) {
                    inventarisSelect.appendChild(option.cloneNode(true));
                }
            });

            // Reset value select inventaris
            inventarisSelect.value = "";
            kondisiSebelumDisplay.value = "";
            kondisiSebelumInput.value = "";
        });
    }

    // Dynamic BHP Row Management
    const addBhpRowBtn = document.getElementById('addBhpRow');
    const container = document.getElementById('bhpRowsContainer');
    const template = document.getElementById('bhpRowTemplate').firstElementChild;
    let rowIndex = 0;

    addBhpRowBtn.addEventListener('click', function() {
        const clone = template.cloneNode(true);
        
        // Atur name attributes dengan index unik
        const select = clone.querySelector('.select-bhp');
        const qty = clone.querySelector('.input-qty');
        const satuanSpan = clone.querySelector('.display-satuan');

        select.setAttribute('name', `bhps[${rowIndex}][bhp_id]`);
        qty.setAttribute('name', `bhps[${rowIndex}][jumlah_digunakan]`);

        // Tampilkan satuan ketika item BHP dipilih
        select.addEventListener('change', function() {
            const selectedOpt = select.options[select.selectedIndex];
            const satuan = selectedOpt.getAttribute('data-satuan');
            const stokMaks = parseInt(selectedOpt.getAttribute('data-stok') || '0');
            
            satuanSpan.textContent = satuan || '-';
            qty.setAttribute('max', stokMaks);
            qty.setAttribute('placeholder', `Maks ${stokMaks}`);
        });

        // Event listener hapus baris
        clone.querySelector('.remove-bhp-row').addEventListener('click', function() {
            clone.remove();
        });

        container.appendChild(clone);
        rowIndex++;
    });

    // Client-side validation to ensure Qty <= Available Stock
    document.getElementById('maintenanceForm').addEventListener('submit', function(e) {
        let valid = true;
        const rows = container.querySelectorAll('.bhp-row');
        
        rows.forEach(row => {
            const select = row.querySelector('.select-bhp');
            const qtyInput = row.querySelector('.input-qty');
            
            if (select.value && qtyInput.value) {
                const option = select.options[select.selectedIndex];
                const stok = parseInt(option.getAttribute('data-stok') || '0');
                const qtyVal = parseInt(qtyInput.value);
                
                if (qtyVal > stok) {
                    alert(`Kuantitas untuk "${option.text.split('(')[0].trim()}" melebihi stok yang tersedia (${stok}).`);
                    qtyInput.focus();
                    valid = false;
                }
            }
        });
        
        if (!valid) {
            e.preventDefault();
        }
    });
});
</script>
@endsection
