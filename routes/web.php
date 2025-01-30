<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SarprasController;
use App\Http\Controllers\TataUsahaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login.index');
});

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'indexRegister'])->name('register.index');
    Route::post('/register/submit', [AuthController::class, 'register'])->name('register.submit');

    Route::get('/login', [AuthController::class, 'indexLogin'])->name('login.index');
    Route::post('/login/submit', [AuthController::class, 'login'])->name('login.submit');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::post('/dashboard/tambah-pengguna', [AdminController::class, 'tambahPengguna'])->name('admin.tambahPengguna');
    Route::put('/dashboard/update-pengguna/{id}', [AdminController::class, 'updatePengguna'])->name('admin.updatePengguna');
    Route::delete('/dashboard/delete/{id}', [AdminController::class, 'delete'])->name('admin.hapusPengguna');

    Route::get('/dashboard/sarpras', [SarprasController::class, 'index'])->name('sarpras.dashboard');
    Route::get('/dashboard/sarpras/prediksi-aset', [SarprasController::class, 'prediksi'])->name('sarpras.prediksi');
    Route::get('/dashboard/sarpras/proses-prediksi', [SarprasController::class, 'prosesPrediksi'])->name('sarpras.proses-prediksi');
    Route::get('/dashboard/sarpras/pengajuan', [SarprasController::class, 'pengajuan'])->name('sarpras.pengajuan');
    Route::post('/dashboard/sarpras/store', [SarprasController::class, 'store'])->name('sarpras.storePrediksi');

    Route::get('/dashboard/tata-usaha', [TataUsahaController::class, 'index'])->name('TU.dashboard');
    Route::get('/dashboard/prediksi-peserta', [TataUsahaController::class, 'prediksi'])->name('TU.prediksi');
    Route::get('/dashboard/proses-prediksi-peserta', [TataUsahaController::class, 'prosesPrediksi'])->name('TU.prosesPrediksi');
    Route::post('/dashboard/simpan-prediksi-peserta', [TataUsahaController::class, 'simpanPrediksi'])->name('TU.simpanPrediksi');
});
