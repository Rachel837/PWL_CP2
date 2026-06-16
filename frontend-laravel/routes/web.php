<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DraftPengadaanController;
use App\Http\Controllers\StokBhpController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\InventarisController;
use App\Http\Middleware\CheckAuth;
use App\Http\Middleware\CheckRoleKalab;
use App\Http\Middleware\CheckRoleKaprodi;
use App\Http\Middleware\CheckRoleAdmin;
use App\Http\Middleware\CheckRoleStafLab;

Route::get('/', function () {
    return redirect()->route('login');
});


// Auth Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard redirection based on roles
Route::get('/dashboard', function () {
    $user = Illuminate\Support\Facades\Session::get('user');
    if (!$user) {
        return redirect()->route('login');
    }
    
    switch ($user['role'] ?? '') {
        case 'administrator':
            return redirect()->route('users.index');
        case 'staf laboratorium':
            return redirect()->route('stok-bhp.index');
        case 'staf administrasi':
            return redirect()->route('penerimaan-barang.index');
        case 'kepala laboratorium':
            return redirect()->route('draft-pengadaan.index');
        case 'ketua program studi':
            return redirect()->route('draft-pengadaan.review.index');
        default:
            return redirect('/');
    }
})->name('dashboard')->middleware([CheckAuth::class]);

// Protected Routes
Route::middleware([CheckAuth::class])->group(function () {
    Route::middleware([CheckRoleAdmin::class])->group(function () {
        Route::resource('users', UserController::class);
    });
    
    Route::resource('ruangan', RuanganController::class);
    
    // Draft Pengadaan Review Routes (restricted to ketua program studi)
    // Must be defined BEFORE Route::resource('draft-pengadaan') to prevent 'review' from being caught as an ID
    Route::middleware([CheckRoleKaprodi::class])->group(function () {
        Route::get('draft-pengadaan/review', [DraftPengadaanController::class, 'reviewIndex'])->name('draft-pengadaan.review.index');
        Route::get('draft-pengadaan/review/{id}', [DraftPengadaanController::class, 'reviewShow'])->name('draft-pengadaan.review.show');
        Route::put('draft-pengadaan/review/{id}/detail/{detailId}', [DraftPengadaanController::class, 'reviewUpdateDetail'])->name('draft-pengadaan.review.update-detail');
        Route::put('draft-pengadaan/review/{id}/finalize', [DraftPengadaanController::class, 'reviewFinalize'])->name('draft-pengadaan.review.finalize');
    });

    // Draft Pengadaan Routes (restricted strictly to kepala laboratorium role)
    Route::middleware([\App\Http\Middleware\CheckRoleKalabOrKaprodi::class])->group(function () {
        Route::get('draft-pengadaan', [DraftPengadaanController::class, 'index'])->name('draft-pengadaan.index');
        Route::get('draft-pengadaan/{draft_pengadaan}', [DraftPengadaanController::class, 'show'])
            ->name('draft-pengadaan.show')
            ->where('draft_pengadaan', '[0-9]+');
        Route::get('draft-pengadaan/history', [DraftPengadaanController::class, 'history'])->name('draft-pengadaan.history');
        Route::get('draft-pengadaan/{id}/terima', [DraftPengadaanController::class, 'prosesPenerimaan'])->name('draft-pengadaan.terima');
        Route::post('draft-pengadaan/{id}/terima', [DraftPengadaanController::class, 'storePenerimaan'])->name('draft-pengadaan.terima.store');
        Route::get('penerimaan-barang', [DraftPengadaanController::class, 'penerimaanIndex'])->name('penerimaan-barang.index');
    });

    Route::middleware([CheckRoleKalab::class])->group(function () {
        Route::resource('draft-pengadaan', DraftPengadaanController::class)->except(['index', 'show']);
        Route::post('draft-pengadaan/{id}/submit', [DraftPengadaanController::class, 'submit'])->name('draft-pengadaan.submit');
        Route::post('draft-pengadaan/{id}/detail', [DraftPengadaanController::class, 'addDetail'])->name('draft-pengadaan.add-detail');
        Route::put('draft-pengadaan-detail/{detailId}', [DraftPengadaanController::class, 'updateDetail'])->name('draft-pengadaan.update-detail');
        Route::delete('draft-pengadaan-detail/{detailId}', [DraftPengadaanController::class, 'deleteDetail'])->name('draft-pengadaan.delete-detail');
        Route::get('draft-pengadaan/{barangId}/inventaris', [DraftPengadaanController::class, 'getReplacementInventaris'])->name('draft-pengadaan.inventaris');
    });

    // Inventory Routes
    Route::get('inventaris', [InventarisController::class, 'index'])->name('inventaris.index');
    Route::get('inventaris/create', [InventarisController::class, 'create'])->name('inventaris.create');
    Route::post('inventaris', [InventarisController::class, 'store'])->name('inventaris.store');
    Route::get('inventaris/{id}/edit', [InventarisController::class, 'edit'])->name('inventaris.edit');
    Route::put('inventaris/{id}', [InventarisController::class, 'update'])->name('inventaris.update');
    Route::delete('inventaris/{id}', [InventarisController::class, 'destroy'])->name('inventaris.destroy');
    Route::get('inventaris/{id}/upload-kondisi', [InventarisController::class, 'showUploadKondisi'])->name('inventaris.upload-kondisi');
    Route::post('inventaris/{id}/upload-kondisi', [InventarisController::class, 'uploadKondisi'])->name('inventaris.upload-kondisi.store');
    Route::post('inventaris/{id}/verifikasi', [InventarisController::class, 'verifikasiKondisi'])->name('inventaris.verifikasi');

    Route::get('stok-bhp', [StokBhpController::class, 'index'])->name('stok-bhp.index');

    // Staf Laboratorium Routes
    Route::middleware([CheckRoleStafLab::class])->group(function () {
        Route::resource('stok-bhp', StokBhpController::class)->except(['index']);
        Route::resource('maintenance', MaintenanceController::class);
    });
});

