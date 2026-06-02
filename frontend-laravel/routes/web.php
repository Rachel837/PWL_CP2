<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DraftPengadaanController;
use App\Http\Middleware\CheckAuth;
use App\Http\Middleware\CheckRoleKalab;

use App\Http\Middleware\CheckRoleKaprodi;

Route::get('/', function () {
    return view('welcome');
});


// Auth Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware([CheckAuth::class])->group(function () {
    Route::resource('users', UserController::class);
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
        Route::get('draft-pengadaan/{draft_pengadaan}', [DraftPengadaanController::class, 'show'])->name('draft-pengadaan.show');
    });

    Route::middleware([CheckRoleKalab::class])->group(function () {
        Route::get('draft-pengadaan/history', [DraftPengadaanController::class, 'history'])->name('draft-pengadaan.history');
        Route::resource('draft-pengadaan', DraftPengadaanController::class)->except(['index', 'show']);
        Route::post('draft-pengadaan/{id}/submit', [DraftPengadaanController::class, 'submit'])->name('draft-pengadaan.submit');
        Route::post('draft-pengadaan/{id}/detail', [DraftPengadaanController::class, 'addDetail'])->name('draft-pengadaan.add-detail');
        Route::put('draft-pengadaan-detail/{detailId}', [DraftPengadaanController::class, 'updateDetail'])->name('draft-pengadaan.update-detail');
        Route::delete('draft-pengadaan-detail/{detailId}', [DraftPengadaanController::class, 'deleteDetail'])->name('draft-pengadaan.delete-detail');
        Route::get('draft-pengadaan/{barangId}/inventaris', [DraftPengadaanController::class, 'getReplacementInventaris'])->name('draft-pengadaan.inventaris');
    });
});

