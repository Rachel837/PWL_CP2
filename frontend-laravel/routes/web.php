<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\CheckAuth;

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
});
