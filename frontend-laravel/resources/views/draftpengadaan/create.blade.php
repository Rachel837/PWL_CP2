@extends('layouts.master')

@section('title', 'Buat Draf Pengadaan Barang - InApp Inventory Dashboard')

@section('content')
<div class="row">
    <div class="col-12 col-lg-8 mx-auto">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fs-3 mb-0">Buat Draf Pengadaan Barang</h1>
            <a href="{{ route('draft-pengadaan.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form action="{{ route('draft-pengadaan.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
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
                        <small class="text-muted block mt-1">Masukkan tahun target pelaksanaan pengadaan barang ini.</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold" for="catatan">
                            Catatan
                        </label>
                        <textarea 
                            class="form-control" 
                            id="catatan" 
                            name="catatan" 
                            rows="4"
                            placeholder="Tuliskan catatan tambahan mengenai draft pengadaan tahunan ini..."
                        >{{ old('catatan') }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            Status Awal: 
                            <span class="badge bg-secondary ms-1">Draft</span>
                        </label>
                    </div>

                    <div class="d-flex gap-2">
                        <button 
                            type="submit" 
                            class="btn btn-primary px-4"
                        >
                            <i class="ti ti-check me-1"></i> Buat Draf Pengadaan
                        </button>
                        <a 
                            href="{{ route('draft-pengadaan.index') }}" 
                            class="btn btn-light px-4"
                        >
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
