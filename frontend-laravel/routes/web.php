<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DraftPengadaanController;
use App\Http\Middleware\CheckAuth;
use App\Http\Middleware\CheckRoleKalab;

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
    
    // Draft Pengadaan Routes (restricted strictly to kepala laboratorium role)
    Route::middleware([CheckRoleKalab::class])->group(function () {
        Route::get('draft-pengadaan/history', [DraftPengadaanController::class, 'history'])->name('draft-pengadaan.history');
        Route::resource('draft-pengadaan', DraftPengadaanController::class);
        Route::post('draft-pengadaan/{id}/submit', [DraftPengadaanController::class, 'submit'])->name('draft-pengadaan.submit');
        Route::post('draft-pengadaan/{id}/detail', [DraftPengadaanController::class, 'addDetail'])->name('draft-pengadaan.add-detail');
        Route::put('draft-pengadaan-detail/{detailId}', [DraftPengadaanController::class, 'updateDetail'])->name('draft-pengadaan.update-detail');
        Route::delete('draft-pengadaan-detail/{detailId}', [DraftPengadaanController::class, 'deleteDetail'])->name('draft-pengadaan.delete-detail');
        Route::get('draft-pengadaan/{barangId}/inventaris', [DraftPengadaanController::class, 'getReplacementInventaris'])->name('draft-pengadaan.inventaris');
    });
});
