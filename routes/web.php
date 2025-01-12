<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SarprasController;
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
    Route::delete('/dashboard/delete/{id}', [AdminController::class, 'delete'])->name('admin.hapusPengguna');

    Route::get('/dashboard/sarpras', [SarprasController::class, 'index'])->name('sarpras.dashboard');
});
